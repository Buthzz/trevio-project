<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Refund extends Model {
    protected $table = 'refunds';

    /**
     * Create refund request
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (booking_id, payment_id, customer_id, refund_amount, reason, bank_name, account_number, account_name, refund_status)
                  VALUES 
                  (:booking_id, :payment_id, :customer_id, :refund_amount, :reason, :bank_name, :account_number, :account_name, 'requested')";
        
        try {
            $this->query($query);
            $this->bind(':booking_id', $data['booking_id']);
            $this->bind(':payment_id', $data['payment_id']);
            $this->bind(':customer_id', $data['customer_id']);
            $this->bind(':refund_amount', $data['refund_amount']);
            $this->bind(':reason', $data['reason']);
            $this->bind(':bank_name', $data['bank_name']);
            $this->bind(':account_number', $data['account_number']);
            $this->bind(':account_name', $data['account_name']);
            
            if ($this->execute()) {
                return (int)$this->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Refund create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all refunds with filters
     * @param string|null $status
     * @return array
     */
    public function getAll($status = null) {
        $query = "SELECT r.*, b.booking_code, b.total_price,
                  u.name as customer_name, u.email as customer_email,
                  h.name as hotel_name
                  FROM {$this->table} r
                  JOIN bookings b ON r.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id";
        
        if ($status) {
            $query .= " WHERE r.refund_status = :status";
        }
        
        $query .= " ORDER BY r.requested_at DESC";
        
        try {
            $this->query($query);
            if ($status) {
                $this->bind(':status', $status);
            }
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Refund getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get pending refunds
     * @return array
     */
    public function getPendingRefunds() {
        return $this->getAll('requested');
    }

    /**
     * Find refund by ID
     * @param int $id
     * @return array|false
     */
    public function find($id) {
        $query = "SELECT r.*, b.booking_code, b.total_price, b.customer_id, b.room_id, b.num_rooms,
                  u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                  h.name as hotel_name, rm.room_type
                  FROM {$this->table} r
                  JOIN bookings b ON r.booking_id = b.id
                  JOIN users u ON b.customer_id = u.id
                  JOIN hotels h ON b.hotel_id = h.id
                  JOIN rooms rm ON b.room_id = rm.id
                  WHERE r.id = :id";
        
        try {
            $this->query($query);
            $this->bind(':id', $id);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Refund find Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find refund by booking ID
     * @param int $bookingId
     * @return array|false
     */
    public function findByBookingId($bookingId) {
        $query = "SELECT * FROM {$this->table} WHERE booking_id = :booking_id";
        
        try {
            $this->query($query);
            $this->bind(':booking_id', $bookingId);
            return $this->single();
        } catch (PDOException $e) {
            error_log("Refund findByBookingId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve refund request (admin action)
     * @param int $refundId
     * @param int $adminId
     * @param string $notes
     * @return bool
     */
    public function approve($refundId, $adminId, $notes = '') {
        $query = "UPDATE {$this->table} 
                  SET refund_status = 'approved',
                      processed_by = :admin_id,
                      approved_at = NOW(),
                      admin_notes = :notes
                  WHERE id = :id AND refund_status = 'requested'";
        
        try {
            $this->query($query);
            $this->bind(':id', $refundId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':notes', $notes);
            return $this->execute();
        } catch (PDOException $e) {
            error_log("Refund approve Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject refund request (admin action)
     * @param int $refundId
     * @param int $adminId
     * @param string $reason
     * @return bool
     */
    public function reject($refundId, $adminId, $reason = '') {
        try {
            $this->beginTransaction();

            // Update refund status
            $query = "UPDATE {$this->table} 
                      SET refund_status = 'rejected',
                          processed_by = :admin_id,
                          rejected_at = NOW(),
                          rejection_reason = :reason
                      WHERE id = :id AND refund_status = 'requested'";
            
            $this->query($query);
            $this->bind(':id', $refundId);
            $this->bind(':admin_id', $adminId);
            $this->bind(':reason', $reason);
            $this->execute();

            $this->commit();
            return true;
        } catch (PDOException $e) {
            $this->rollBack();
            error_log("Refund reject Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete refund process - upload transfer receipt (ATOMIC TRANSACTION)
     * @param int $refundId
     * @param string $receiptFile
     * @param int $adminId
     * @return bool
     */
    public function complete($refundId, $receiptFile, $adminId) {
        try {
            $this->beginTransaction();

            // 1. Get refund details
            $refund = $this->find($refundId);
            if (!$refund || $refund['refund_status'] !== 'approved') {
                throw new \Exception("Refund not found or not in approved status");
            }

            // 2. Update refund status to completed
            $query = "UPDATE {$this->table} 
                      SET refund_status = 'completed',
                          refund_receipt = :receipt,
                          completed_at = NOW(),
                          processed_by = :admin_id
                      WHERE id = :id";
            
            $this->query($query);
            $this->bind(':id', $refundId);
            $this->bind(':receipt', $receiptFile);
            $this->bind(':admin_id', $adminId);
            $this->execute();

            // 3. Update booking status to 'refunded'
            $this->query("UPDATE bookings SET booking_status = 'refunded' WHERE id = :booking_id");
            $this->bind(':booking_id', $refund['booking_id']);
            $this->execute();

            // 4. Restore room slots (ATOMIC)
            $this->query("UPDATE rooms 
                             SET available_slots = available_slots + :num_rooms
                             WHERE id = :room_id");
            $this->bind(':room_id', $refund['room_id']);
            $this->bind(':num_rooms', $refund['num_rooms']);
            $this->execute();

            $this->commit();
            return true;

        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Refund complete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count pending refunds for dashboard
     * @return int
     */
    public function countPending() {
        try {
            $this->query("SELECT COUNT(*) as total FROM {$this->table} WHERE refund_status = 'requested'");
            $result = $this->single();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Refund countPending Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get refunds by customer ID
     * @param int $customerId
     * @return array
     */
    public function getByCustomer($customerId) {
        $query = "SELECT r.*, b.booking_code, h.name as hotel_name
                  FROM {$this->table} r
                  JOIN bookings b ON r.booking_id = b.id
                  JOIN hotels h ON b.hotel_id = h.id
                  WHERE r.customer_id = :customer_id
                  ORDER BY r.requested_at DESC";
        
        try {
            $this->query($query);
            $this->bind(':customer_id', $customerId);
            return $this->resultSet();
        } catch (PDOException $e) {
            error_log("Refund getByCustomer Error: " . $e->getMessage());
            return [];
        }
    }
}
