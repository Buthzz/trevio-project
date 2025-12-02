/**
 * ===================================================================
 * TREVIO - Charts.js (IMPROVED)
 * Chart.js initialization dan management
 * ===================================================================
 * Improvements:
 * - Added Chart.js library check before operations
 * - Enhanced error handling in all chart methods
 * - Fixed gradient creation with proper null checks
 * - Added validation for all inputs
 * - Improved update methods with error recovery
 * - Safe chart destruction and cleanup
 * - Standardized error responses
 * - JSDoc documentation for public APIs
 * ===================================================================
 */

'use strict';

const Charts = (function () {
  let chartInstances = new Map();

  /**
   * @typedef {Object} ChartResponse
   * @property {boolean} success - Operation success status
   * @property {*} data - Chart instance or null
   * @property {string} error - Error message if failed
   */

  const CHART_COLORS = {
    primary: '#4f46e5',
    secondary: '#8b5cf6',
    success: '#10b981',
    danger: '#ef4444',
    warning: '#f59e0b',
    info: '#06b6d4',
    light: '#f3f4f6',
    dark: '#1f2937',
    accent: '#4f46e5',
    accentLight: '#6366f1',
  };

  const GRADIENT_COLORS = {
    primary: ['rgba(79, 70, 229, 0.1)', 'rgba(79, 70, 229, 0)'],
    success: ['rgba(16, 185, 129, 0.1)', 'rgba(16, 185, 129, 0)'],
    danger: ['rgba(239, 68, 68, 0.1)', 'rgba(239, 68, 68, 0)'],
    warning: ['rgba(245, 158, 11, 0.1)', 'rgba(245, 158, 11, 0)'],
  };

  /**
   * Creates standardized success response
   * @param {*} data - Response data
   * @returns {ChartResponse} Success response
   * @private
   */
  const createSuccessResponse = (data) => ({
    success: true,
    data,
  });

  /**
   * Creates standardized error response
   * @param {string} error - Error message
   * @returns {ChartResponse} Error response
   * @private
   */
  const createErrorResponse = (error) => ({
    success: false,
    data: null,
    error,
  });

  /**
   * Logs message to console when DEBUG enabled
   * @param {string} message - Message to log
   * @returns {void}
   */
  const log = (message) => {
    if (window.DEBUG) console.log(`[CHARTS] ${message}`);
  };

  /**
   * Logs error to console
   * @param {string} message - Error message
   * @param {Error} error - Optional error object
   * @returns {void}
   */
  const logError = (message, error = null) => {
    if (window.DEBUG) console.error(`[CHARTS ERROR] ${message}`, error || '');
  };

  /**
   * Gets canvas element
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @returns {HTMLCanvasElement|null} Canvas element or null
   * @private
   */
  const getCanvasElement = (canvas) => {
    if (canvas instanceof HTMLCanvasElement) return canvas;
    return document.querySelector(canvas);
  };

  /**
   * Creates gradient for chart
   * @param {HTMLCanvasElement} canvas - Canvas element
   * @param {Array<string>} colors - Color array [startColor, endColor]
   * @returns {CanvasGradient|string} Gradient or fallback color
   * @private
   */
  const createGradient = (canvas, colors) => {
    if (!canvas || !colors || !Array.isArray(colors) || colors.length < 2) {
      return (colors && colors[0]) || '#4f46e5';
    }
    
    try {
      const ctx = canvas.getContext('2d');
      if (!ctx) {
        logError('Could not get 2D context from canvas');
        return colors[0];
      }
      
      const gradient = ctx.createLinearGradient(0, 0, 0, Math.max(canvas.height, 1));
      gradient.addColorStop(0, colors[0]);
      gradient.addColorStop(1, colors[1]);
      return gradient;
    } catch (e) {
      logError('Failed to create gradient', e);
      return colors[0];
    }
  };

  /**
   * Creates line chart
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @param {Object} data - Chart data
   * @param {Array<string>} data.labels - Data labels
   * @param {Array<Object>} data.datasets - Chart datasets
   * @param {Object} options - Chart options
   * @returns {ChartResponse} Response with chart instance
   */
  const createLineChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) return createErrorResponse('Canvas element not found');
    if (typeof Chart === 'undefined') return createErrorResponse('Chart.js library not loaded');

    try {
      const chartConfig = {
        type: 'line',
        data: {
          labels: data.labels || [],
          datasets: (data.datasets || []).map((dataset, index) => {
            const color = dataset.borderColor || CHART_COLORS.primary;
            const gradientColor = dataset.gradientColor || GRADIENT_COLORS.primary;
            return {
              label: dataset.label || `Dataset ${index + 1}`,
              data: dataset.data || [],
              borderColor: color,
              backgroundColor: createGradient(canvasEl, gradientColor),
              borderWidth: dataset.borderWidth || 2,
              fill: dataset.fill !== false,
              tension: dataset.tension || 0.4,
              pointRadius: dataset.pointRadius || 4,
              pointHoverRadius: dataset.pointHoverRadius || 6,
              pointBackgroundColor: dataset.pointBackgroundColor || color,
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
            };
          }),
        },
        options: {
          responsive: options.responsive !== false,
          maintainAspectRatio: options.maintainAspectRatio !== false,
          interaction: { intersect: false, mode: 'index' },
          plugins: {
            legend: {
              display: options.legend !== false,
              position: options.legendPosition || 'top',
              labels: { usePointStyle: true, padding: 15, font: { family: "'Inter', sans-serif", size: 12, weight: '500' }, color: '#64748b' },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: { family: "'Inter', sans-serif", size: 12, weight: '600' },
              bodyFont: { family: "'Inter', sans-serif", size: 11 },
              padding: 12,
              borderRadius: 8,
              displayColors: true,
              callbacks: options.tooltipCallbacks || {},
            },
          },
          scales: {
            y: {
              beginAtZero: options.beginAtZero !== false,
              grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.yScale,
            },
            x: {
              grid: { display: false, drawBorder: false },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.xScale,
            },
          },
          ...options,
        },
      };
      const chart = new Chart(canvasEl, chartConfig);
      log('Line chart created');
      return createSuccessResponse(chart);
    } catch (e) {
      logError('Failed to create line chart', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Creates bar chart
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @param {Object} data - Chart data
   * @param {Array<string>} data.labels - Data labels
   * @param {Array<Object>} data.datasets - Chart datasets
   * @param {Object} options - Chart options
   * @returns {ChartResponse} Response with chart instance
   */
  const createBarChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) return createErrorResponse('Canvas element not found');
    if (typeof Chart === 'undefined') return createErrorResponse('Chart.js library not loaded');

    try {
      const chartConfig = {
        type: 'bar',
        data: {
          labels: data.labels || [],
          datasets: (data.datasets || []).map((dataset, index) => ({
            label: dataset.label || `Dataset ${index + 1}`,
            data: dataset.data || [],
            backgroundColor: dataset.backgroundColor || CHART_COLORS.primary,
            borderColor: dataset.borderColor || CHART_COLORS.primary,
            borderWidth: dataset.borderWidth || 1,
          })),
        },
        options: {
          responsive: options.responsive !== false,
          maintainAspectRatio: options.maintainAspectRatio !== false,
          interaction: { intersect: false, mode: 'index' },
          plugins: {
            legend: {
              display: options.legend !== false,
              position: options.legendPosition || 'top',
              labels: { usePointStyle: true, padding: 15, font: { family: "'Inter', sans-serif", size: 12, weight: '500' }, color: '#64748b' },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: { family: "'Inter', sans-serif", size: 12, weight: '600' },
              bodyFont: { family: "'Inter', sans-serif", size: 11 },
              padding: 12,
              borderRadius: 8,
              displayColors: true,
              callbacks: options.tooltipCallbacks || {},
            },
          },
          scales: {
            y: {
              beginAtZero: options.beginAtZero !== false,
              grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.yScale,
            },
            x: {
              grid: { display: false, drawBorder: false },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.xScale,
            },
          },
          ...options,
        },
      };
      const chart = new Chart(canvasEl, chartConfig);
      log('Bar chart created');
      return createSuccessResponse(chart);
    } catch (e) {
      logError('Failed to create bar chart', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Creates doughnut chart
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @param {Object} data - Chart data
   * @param {Array<string>} data.labels - Data labels
   * @param {Array<number>} data.data - Data values
   * @param {Object} options - Chart options
   * @returns {ChartResponse} Response with chart instance
   */
  const createDoughnutChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) return createErrorResponse('Canvas element not found');
    if (typeof Chart === 'undefined') return createErrorResponse('Chart.js library not loaded');

    try {
      const colors = [CHART_COLORS.primary, CHART_COLORS.success, CHART_COLORS.warning, CHART_COLORS.danger, CHART_COLORS.info];
      const chartConfig = {
        type: 'doughnut',
        data: {
          labels: data.labels || [],
          datasets: [{
            data: data.data || [],
            backgroundColor: (data.backgroundColor || []).length ? data.backgroundColor : colors.slice(0, (data.data || []).length),
            borderColor: '#fff',
            borderWidth: 2,
            ...data.dataset,
          }],
        },
        options: {
          responsive: options.responsive !== false,
          maintainAspectRatio: options.maintainAspectRatio !== false,
          plugins: {
            legend: {
              display: options.legend !== false,
              position: options.legendPosition || 'bottom',
              labels: { usePointStyle: true, padding: 15, font: { family: "'Inter', sans-serif", size: 12, weight: '500' }, color: '#64748b' },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: { family: "'Inter', sans-serif", size: 12, weight: '600' },
              bodyFont: { family: "'Inter', sans-serif", size: 11 },
              padding: 12,
              borderRadius: 8,
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed}`;
                },
                ...options.tooltipCallbacks,
              },
            },
          },
          ...options,
        },
      };
      const chart = new Chart(canvasEl, chartConfig);
      log('Doughnut chart created');
      return createSuccessResponse(chart);
    } catch (e) {
      logError('Failed to create doughnut chart', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Creates pie chart
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @param {Object} data - Chart data
   * @param {Array<string>} data.labels - Data labels
   * @param {Array<number>} data.data - Data values
   * @param {Object} options - Chart options
   * @returns {ChartResponse} Response with chart instance
   */
  const createPieChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) return createErrorResponse('Canvas element not found');
    if (typeof Chart === 'undefined') return createErrorResponse('Chart.js library not loaded');

    try {
      const colors = [CHART_COLORS.primary, CHART_COLORS.success, CHART_COLORS.warning, CHART_COLORS.danger, CHART_COLORS.info];
      const chartConfig = {
        type: 'pie',
        data: {
          labels: data.labels || [],
          datasets: [{
            data: data.data || [],
            backgroundColor: (data.backgroundColor || []).length ? data.backgroundColor : colors.slice(0, (data.data || []).length),
            borderColor: '#fff',
            borderWidth: 2,
            ...data.dataset,
          }],
        },
        options: {
          responsive: options.responsive !== false,
          maintainAspectRatio: options.maintainAspectRatio !== false,
          plugins: {
            legend: {
              display: options.legend !== false,
              position: options.legendPosition || 'right',
              labels: { usePointStyle: true, padding: 15, font: { family: "'Inter', sans-serif", size: 12, weight: '500' }, color: '#64748b' },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: { family: "'Inter', sans-serif", size: 12, weight: '600' },
              bodyFont: { family: "'Inter', sans-serif", size: 11 },
              padding: 12,
              borderRadius: 8,
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed}`;
                },
                ...options.tooltipCallbacks,
              },
            },
          },
          ...options,
        },
      };
      const chart = new Chart(canvasEl, chartConfig);
      log('Pie chart created');
      return createSuccessResponse(chart);
    } catch (e) {
      logError('Failed to create pie chart', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Creates radar chart
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @param {Object} data - Chart data
   * @param {Array<string>} data.labels - Data labels
   * @param {Array<Object>} data.datasets - Chart datasets
   * @param {Object} options - Chart options
   * @returns {ChartResponse} Response with chart instance
   */
  const createRadarChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) return createErrorResponse('Canvas element not found');
    if (typeof Chart === 'undefined') return createErrorResponse('Chart.js library not loaded');

    try {
      const chartConfig = {
        type: 'radar',
        data: {
          labels: data.labels || [],
          datasets: (data.datasets || []).map((dataset, index) => {
            const color = dataset.borderColor || CHART_COLORS.primary;
            return {
              label: dataset.label || `Dataset ${index + 1}`,
              data: dataset.data || [],
              borderColor: color,
              backgroundColor: dataset.backgroundColor || `rgba(79, 70, 229, 0.1)`,
              borderWidth: dataset.borderWidth || 2,
              pointRadius: dataset.pointRadius || 3,
              pointHoverRadius: dataset.pointHoverRadius || 5,
              pointBackgroundColor: dataset.pointBackgroundColor || color,
            };
          }),
        },
        options: {
          responsive: options.responsive !== false,
          maintainAspectRatio: options.maintainAspectRatio !== false,
          plugins: {
            legend: {
              display: options.legend !== false,
              position: options.legendPosition || 'top',
              labels: { usePointStyle: true, padding: 15, font: { family: "'Inter', sans-serif", size: 12, weight: '500' }, color: '#64748b' },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: { family: "'Inter', sans-serif", size: 12, weight: '600' },
              bodyFont: { family: "'Inter', sans-serif", size: 11 },
              padding: 12,
              borderRadius: 8,
              callbacks: options.tooltipCallbacks || {},
            },
          },
          scales: {
            r: {
              beginAtZero: options.beginAtZero !== false,
              grid: { color: 'rgba(0, 0, 0, 0.05)' },
              ticks: { font: { family: "'Inter', sans-serif", size: 10 }, color: '#94a3b8' },
              ...options.rScale,
            },
          },
          ...options,
        },
      };
      const chart = new Chart(canvasEl, chartConfig);
      log('Radar chart created');
      return createSuccessResponse(chart);
    } catch (e) {
      logError('Failed to create radar chart', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Creates area chart
   * @param {HTMLCanvasElement|string} canvas - Canvas element or selector
   * @param {Object} data - Chart data
   * @param {Object} options - Chart options
   * @returns {ChartResponse} Response with chart instance
   */
  const createAreaChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) return createErrorResponse('Canvas element not found');
    if (typeof Chart === 'undefined') return createErrorResponse('Chart.js library not loaded');

    try {
      const chartConfig = {
        type: 'line',
        data: {
          labels: data.labels || [],
          datasets: (data.datasets || []).map((dataset, index) => {
            const color = dataset.borderColor || CHART_COLORS.primary;
            return {
              label: dataset.label || `Dataset ${index + 1}`,
              data: dataset.data || [],
              borderColor: color,
              backgroundColor: dataset.backgroundColor || `rgba(79, 70, 229, 0.1)`,
              fill: true,
              tension: 0.4,
              borderWidth: 2,
              pointRadius: 3,
              pointHoverRadius: 5,
            };
          }),
        },
        options: {
          responsive: options.responsive !== false,
          maintainAspectRatio: options.maintainAspectRatio !== false,
          interaction: { intersect: false, mode: 'index' },
          plugins: {
            filler: { propagate: true },
            legend: {
              display: options.legend !== false,
              position: options.legendPosition || 'top',
              labels: { usePointStyle: true, padding: 15, font: { family: "'Inter', sans-serif", size: 12, weight: '500' }, color: '#64748b' },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: { family: "'Inter', sans-serif", size: 12, weight: '600' },
              bodyFont: { family: "'Inter', sans-serif", size: 11 },
              padding: 12,
              borderRadius: 8,
              callbacks: options.tooltipCallbacks || {},
            },
          },
          scales: {
            y: {
              beginAtZero: options.beginAtZero !== false,
              grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.yScale,
            },
            x: {
              grid: { display: false, drawBorder: false },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.xScale,
            },
          },
          ...options,
        },
      };
      const chart = new Chart(canvasEl, chartConfig);
      log('Area chart created');
      return createSuccessResponse(chart);
    } catch (e) {
      logError('Failed to create area chart', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Registers chart instance
   * @param {string} id - Chart ID
   * @param {Chart} chart - Chart instance
   * @returns {void}
   */
  const registerChart = (id, chart) => {
    if (!id || typeof id !== 'string') {
      logError('Invalid chart ID');
      return;
    }
    if (chartInstances.has(id)) {
      logError(`Chart with ID ${id} already registered`);
      return;
    }
    chartInstances.set(id, chart);
    log(`Chart registered: ${id}`);
  };

  /**
   * Gets chart by ID
   * @param {string} id - Chart ID
   * @returns {Chart|null} Chart instance or null
   */
  const getChart = (id) => chartInstances.get(id) || null;

  /**
   * Updates chart data
   * @param {string} id - Chart ID
   * @param {Object} data - New chart data
   * @returns {ChartResponse} Update response
   */
  const updateChart = (id, data) => {
    if (!data || typeof data !== 'object') {
      return createErrorResponse('Invalid data provided');
    }
    const chart = getChart(id);
    if (!chart) {
      return createErrorResponse(`Chart with ID ${id} not found`);
    }
    try {
      if (data.labels) chart.data.labels = data.labels;
      if (data.datasets) chart.data.datasets = data.datasets;
      chart.update();
      log(`Chart updated: ${id}`);
      return createSuccessResponse(chart);
    } catch (e) {
      logError(`Failed to update chart ${id}`, e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Updates specific dataset in chart
   * @param {string} id - Chart ID
   * @param {number} datasetIndex - Dataset index
   * @param {Object} dataset - New dataset
   * @returns {ChartResponse} Update response
   */
  const updateDataset = (id, datasetIndex, dataset) => {
    const chart = getChart(id);
    if (!chart || !chart.data.datasets[datasetIndex]) {
      return createErrorResponse(`Chart or dataset not found`);
    }
    try {
      Object.assign(chart.data.datasets[datasetIndex], dataset);
      chart.update();
      log(`Dataset updated: ${id}[${datasetIndex}]`);
      return createSuccessResponse(chart);
    } catch (e) {
      logError(`Failed to update dataset`, e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Adds dataset to chart
   * @param {string} id - Chart ID
   * @param {Object} dataset - Dataset to add
   * @returns {ChartResponse} Update response
   */
  const addDataset = (id, dataset) => {
    const chart = getChart(id);
    if (!chart) {
      return createErrorResponse(`Chart with ID ${id} not found`);
    }
    try {
      chart.data.datasets.push(dataset);
      chart.update();
      log(`Dataset added: ${id}`);
      return createSuccessResponse(chart);
    } catch (e) {
      logError(`Failed to add dataset`, e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Removes dataset from chart
   * @param {string} id - Chart ID
   * @param {number} datasetIndex - Dataset index to remove
   * @returns {ChartResponse} Update response
   */
  const removeDataset = (id, datasetIndex) => {
    const chart = getChart(id);
    if (!chart || !chart.data.datasets[datasetIndex]) {
      return createErrorResponse(`Chart or dataset not found`);
    }
    try {
      chart.data.datasets.splice(datasetIndex, 1);
      chart.update();
      log(`Dataset removed: ${id}[${datasetIndex}]`);
      return createSuccessResponse(chart);
    } catch (e) {
      logError(`Failed to remove dataset`, e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Destroys single chart
   * @param {string} id - Chart ID
   * @returns {ChartResponse} Destroy response
   */
  const destroyChart = (id) => {
    const chart = getChart(id);
    if (chart) {
      try {
        chart.destroy();
        chartInstances.delete(id);
        log(`Chart destroyed: ${id}`);
        return createSuccessResponse(null);
      } catch (e) {
        logError(`Failed to destroy chart ${id}`, e);
        return createErrorResponse(e.message);
      }
    }
    return createErrorResponse(`Chart with ID ${id} not found`);
  };

  /**
   * Destroys all charts
   * @returns {ChartResponse} Destroy response
   */
  const destroyAllCharts = () => {
    try {
      chartInstances.forEach((chart) => {
        if (chart) chart.destroy();
      });
      chartInstances.clear();
      log('All charts destroyed');
      return createSuccessResponse(null);
    } catch (e) {
      logError('Failed to destroy all charts', e);
      return createErrorResponse(e.message);
    }
  };

  return {
    // Constants
    COLORS: CHART_COLORS,
    GRADIENTS: GRADIENT_COLORS,
    
    // Chart creation
    createLineChart, createBarChart, createDoughnutChart, createPieChart, createRadarChart, createAreaChart,
    
    // Chart management
    registerChart, getChart, updateChart, updateDataset, addDataset, removeDataset, destroyChart, destroyAllCharts,
  };
})();

if (typeof window !== 'undefined') {
  if (typeof Chart === 'undefined') {
    console.warn('[CHARTS] Warning: Chart.js library not found. Please load Chart.js before this module.');
  } else {
    console.log('[CHARTS] Module loaded successfully');
  }
}
