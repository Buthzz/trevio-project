/**
 * ===================================================================
 * TREVIO - Charts.js
 * Chart.js initialization dan management
 * ===================================================================
 * Fitur:
 * - Initialize berbagai jenis charts
 * - Chart data management
 * - Responsive chart handling
 * - Chart update dan destroy
 * - Chart theme customization
 * ===================================================================
 */

'use strict';

/**
 * Charts Global Object
 * Menyediakan chart utilities menggunakan Chart.js library
 */
const Charts = (function () {
  // ================================================================
  // PRIVATE VARIABLES
  // ================================================================
  let chartInstances = new Map();
  let chartDefaults = {};

  // ================================================================
  // CHART COLOR THEME
  // ================================================================
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

  // ================================================================
  // PRIVATE HELPERS
  // ================================================================

  /**
   * Safe console log
   */
  const log = (message) => {
    if (window.DEBUG) {
      console.log(`[CHARTS] ${message}`);
    }
  };

  /**
   * Dapatkan element canvas
   * @param {string|HTMLCanvasElement} canvas - Canvas selector atau element
   * @returns {HTMLCanvasElement|null}
   */
  const getCanvasElement = (canvas) => {
    if (canvas instanceof HTMLCanvasElement) {
      return canvas;
    }
    return document.querySelector(canvas);
  };

  /**
   * Dapatkan canvas 2D context dengan gradient
   * @param {HTMLCanvasElement} canvas
   * @param {Array} colors - Array of colors [start, end]
   * @returns {CanvasGradient}
   */
  const createGradient = (canvas, colors) => {
    if (!canvas || !colors || colors.length < 2) {
      console.error('Invalid canvas or colors for gradient');
      return colors ? colors[0] : '#4f46e5';
    }
    
    const ctx = canvas.getContext('2d');
    if (!ctx) {
      console.error('Could not get 2D context from canvas');
      return colors[0];
    }
    
    try {
      const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
      gradient.addColorStop(0, colors[0]);
      gradient.addColorStop(1, colors[1]);
      return gradient;
    } catch (e) {
      console.error('Failed to create gradient', e);
      return colors[0];
    }
  };

  // ================================================================
  // CHART FACTORY METHODS
  // ================================================================

  /**
   * Create Line Chart
   * @param {string|HTMLCanvasElement} canvas - Canvas element
   * @param {Object} data - Chart data config
   * @param {Object} options - Chart options
   * @returns {Chart|null}
   */
  const createLineChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) {
      console.error('Canvas element not found');
      return null;
    }

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: true,
      tension: 0.4,
      fill: true,
      borderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      pointBackgroundColor: CHART_COLORS.primary,
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
    };

    const chartConfig = {
      type: 'line',
      data: {
        labels: data.labels || [],
        datasets: (data.datasets || []).map((dataset, index) => {
          const color =
            dataset.borderColor || CHART_COLORS.primary;
          const gradientColor =
            dataset.gradientColor || GRADIENT_COLORS.primary;

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
            ...dataset,
          };
        }),
      },
      options: {
        responsive: options.responsive !== false,
        maintainAspectRatio: options.maintainAspectRatio !== false,
        interaction: {
          intersect: false,
          mode: 'index',
        },
        plugins: {
          legend: {
            display: options.legend !== false,
            position: options.legendPosition || 'top',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                family: "'Inter', sans-serif",
                size: 12,
                weight: '500',
              },
              color: '#64748b',
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '600',
            },
            bodyFont: {
              family: "'Inter', sans-serif",
              size: 11,
            },
            padding: 12,
            borderRadius: 8,
            displayColors: true,
            callbacks: options.tooltipCallbacks || {},
          },
        },
        scales: {
          y: {
            beginAtZero: options.beginAtZero !== false,
            grid: {
              drawBorder: false,
              color: 'rgba(0, 0, 0, 0.05)',
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 11,
              },
              color: '#94a3b8',
            },
            ...options.yScale,
          },
          x: {
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 11,
              },
              color: '#94a3b8',
            },
            ...options.xScale,
          },
        },
        ...options,
      },
    };

    const chart = new Chart(canvasEl, chartConfig);
    log('Line chart created');
    return chart;
  };

  /**
   * Create Bar Chart
   * @param {string|HTMLCanvasElement} canvas - Canvas element
   * @param {Object} data - Chart data config
   * @param {Object} options - Chart options
   * @returns {Chart|null}
   */
  const createBarChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) {
      console.error('Canvas element not found');
      return null;
    }

    const chartConfig = {
      type: 'bar',
      data: {
        labels: data.labels || [],
        datasets: (data.datasets || []).map((dataset, index) => {
          const colors = Array.isArray(dataset.backgroundColor)
            ? dataset.backgroundColor
            : [dataset.backgroundColor || CHART_COLORS.primary];

          return {
            label: dataset.label || `Dataset ${index + 1}`,
            data: dataset.data || [],
            backgroundColor: colors,
            borderColor: colors.map((c) => c.replace('0.6', '1')),
            borderWidth: 1,
            borderRadius: 8,
            borderSkipped: false,
            ...dataset,
          };
        }),
      },
      options: {
        responsive: options.responsive !== false,
        maintainAspectRatio: options.maintainAspectRatio !== false,
        interaction: {
          intersect: false,
          mode: 'index',
        },
        plugins: {
          legend: {
            display: options.legend !== false,
            position: options.legendPosition || 'top',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                family: "'Inter', sans-serif",
                size: 12,
                weight: '500',
              },
              color: '#64748b',
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '600',
            },
            bodyFont: {
              family: "'Inter', sans-serif",
              size: 11,
            },
            padding: 12,
            borderRadius: 8,
            displayColors: true,
            callbacks: options.tooltipCallbacks || {},
          },
        },
        scales: {
          y: {
            beginAtZero: options.beginAtZero !== false,
            grid: {
              drawBorder: false,
              color: 'rgba(0, 0, 0, 0.05)',
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 11,
              },
              color: '#94a3b8',
            },
            ...options.yScale,
          },
          x: {
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 11,
              },
              color: '#94a3b8',
            },
            ...options.xScale,
          },
        },
        ...options,
      },
    };

    const chart = new Chart(canvasEl, chartConfig);
    log('Bar chart created');
    return chart;
  };

  /**
   * Create Doughnut Chart
   * @param {string|HTMLCanvasElement} canvas - Canvas element
   * @param {Object} data - Chart data config
   * @param {Object} options - Chart options
   * @returns {Chart|null}
   */
  const createDoughnutChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) {
      console.error('Canvas element not found');
      return null;
    }

    const colors = [
      CHART_COLORS.primary,
      CHART_COLORS.success,
      CHART_COLORS.warning,
      CHART_COLORS.danger,
      CHART_COLORS.info,
    ];

    const chartConfig = {
      type: 'doughnut',
      data: {
        labels: data.labels || [],
        datasets: [
          {
            data: data.data || [],
            backgroundColor: (data.backgroundColor || []).length
              ? data.backgroundColor
              : colors.slice(0, (data.data || []).length),
            borderColor: '#fff',
            borderWidth: 2,
            ...data.dataset,
          },
        ],
      },
      options: {
        responsive: options.responsive !== false,
        maintainAspectRatio: options.maintainAspectRatio !== false,
        plugins: {
          legend: {
            display: options.legend !== false,
            position: options.legendPosition || 'bottom',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                family: "'Inter', sans-serif",
                size: 12,
                weight: '500',
              },
              color: '#64748b',
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '600',
            },
            bodyFont: {
              family: "'Inter', sans-serif",
              size: 11,
            },
            padding: 12,
            borderRadius: 8,
            callbacks: {
              label: function (context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return `${label}: ${value} (${percentage}%)`;
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
    return chart;
  };

  /**
   * Create Pie Chart
   * @param {string|HTMLCanvasElement} canvas - Canvas element
   * @param {Object} data - Chart data config
   * @param {Object} options - Chart options
   * @returns {Chart|null}
   */
  const createPieChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) {
      console.error('Canvas element not found');
      return null;
    }

    const colors = [
      CHART_COLORS.primary,
      CHART_COLORS.success,
      CHART_COLORS.warning,
      CHART_COLORS.danger,
      CHART_COLORS.info,
    ];

    const chartConfig = {
      type: 'pie',
      data: {
        labels: data.labels || [],
        datasets: [
          {
            data: data.data || [],
            backgroundColor: (data.backgroundColor || []).length
              ? data.backgroundColor
              : colors.slice(0, (data.data || []).length),
            borderColor: '#fff',
            borderWidth: 2,
            ...data.dataset,
          },
        ],
      },
      options: {
        responsive: options.responsive !== false,
        maintainAspectRatio: options.maintainAspectRatio !== false,
        plugins: {
          legend: {
            display: options.legend !== false,
            position: options.legendPosition || 'right',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                family: "'Inter', sans-serif",
                size: 12,
                weight: '500',
              },
              color: '#64748b',
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '600',
            },
            bodyFont: {
              family: "'Inter', sans-serif",
              size: 11,
            },
            padding: 12,
            borderRadius: 8,
            callbacks: {
              label: function (context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return `${label}: ${value} (${percentage}%)`;
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
    return chart;
  };

  /**
   * Create Radar Chart
   * @param {string|HTMLCanvasElement} canvas - Canvas element
   * @param {Object} data - Chart data config
   * @param {Object} options - Chart options
   * @returns {Chart|null}
   */
  const createRadarChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) {
      console.error('Canvas element not found');
      return null;
    }

    const chartConfig = {
      type: 'radar',
      data: {
        labels: data.labels || [],
        datasets: (data.datasets || []).map((dataset, index) => {
          const color = dataset.borderColor || CHART_COLORS.primary;
          let bgColor = color;
          
          // Safe color conversion
          try {
            if (typeof color === 'string') {
              if (color.includes('rgba')) {
                bgColor = color.replace(/,[^,]*\)$/, ', 0.1)');
              } else if (color.includes('rgb')) {
                bgColor = color.replace(/\)$/, ', 0.1)').replace('rgb', 'rgba');
              } else if (color.startsWith('#')) {
                // Fallback for hex colors - just use original color
                bgColor = color;
              }
            }
          } catch (e) {
            bgColor = color; // Use original on error
          }
          
          return {
            label: dataset.label || `Dataset ${index + 1}`,
            data: dataset.data || [],
            borderColor: color,
            backgroundColor: bgColor,
            borderWidth: 2,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            ...dataset,
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
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                family: "'Inter', sans-serif",
                size: 12,
                weight: '500',
              },
              color: '#64748b',
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '600',
            },
            bodyFont: {
              family: "'Inter', sans-serif",
              size: 11,
            },
            padding: 12,
            borderRadius: 8,
            callbacks: options.tooltipCallbacks || {},
          },
        },
        scales: {
          r: {
            beginAtZero: options.beginAtZero !== false,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)',
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 10,
              },
              color: '#94a3b8',
            },
            ...options.rScale,
          },
        },
        ...options,
      },
    };

    const chart = new Chart(canvasEl, chartConfig);
    log('Radar chart created');
    return chart;
  };

  /**
   * Create Area Chart
   * @param {string|HTMLCanvasElement} canvas - Canvas element
   * @param {Object} data - Chart data config
   * @param {Object} options - Chart options
   * @returns {Chart|null}
   */
  const createAreaChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) {
      console.error('Canvas element not found');
      return null;
    }

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
            fill: true,
            tension: dataset.tension || 0.4,
            pointRadius: dataset.pointRadius || 3,
            pointHoverRadius: dataset.pointHoverRadius || 5,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
            pointBorderWidth: 1,
            ...dataset,
          };
        }),
      },
      options: {
        responsive: options.responsive !== false,
        maintainAspectRatio: options.maintainAspectRatio !== false,
        interaction: {
          intersect: false,
          mode: 'index',
        },
        plugins: {
          filler: {
            propagate: true,
          },
          legend: {
            display: options.legend !== false,
            position: options.legendPosition || 'top',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                family: "'Inter', sans-serif",
                size: 12,
                weight: '500',
              },
              color: '#64748b',
            },
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
              family: "'Inter', sans-serif",
              size: 12,
              weight: '600',
            },
            bodyFont: {
              family: "'Inter', sans-serif",
              size: 11,
            },
            padding: 12,
            borderRadius: 8,
            displayColors: true,
            callbacks: options.tooltipCallbacks || {},
          },
        },
        scales: {
          y: {
            beginAtZero: options.beginAtZero !== false,
            stacked: options.stacked || false,
            grid: {
              drawBorder: false,
              color: 'rgba(0, 0, 0, 0.05)',
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 11,
              },
              color: '#94a3b8',
            },
            ...options.yScale,
          },
          x: {
            stacked: options.stacked || false,
            grid: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              font: {
                family: "'Inter', sans-serif",
                size: 11,
              },
              color: '#94a3b8',
            },
            ...options.xScale,
          },
        },
        ...options,
      },
    };

    const chart = new Chart(canvasEl, chartConfig);
    log('Area chart created');
    return chart;
  };

  // ================================================================
  // CHART MANAGEMENT
  // ================================================================

  /**
   * Register chart instance
   * @param {string} id - Chart ID
   * @param {Chart} chart - Chart instance
   */
  const registerChart = (id, chart) => {
    if (chartInstances.has(id)) {
      destroyChart(id);
    }
    chartInstances.set(id, chart);
    log(`Chart registered: ${id}`);
  };

  /**
   * Get chart instance
   * @param {string} id - Chart ID
   * @returns {Chart|null}
   */
  const getChart = (id) => {
    return chartInstances.get(id) || null;
  };

  /**
   * Update chart data
   * @param {string} id - Chart ID
   * @param {Object} data - New data
   */
  const updateChart = (id, data) => {
    const chart = getChart(id);
    if (!chart) return;

    if (data.labels) {
      chart.data.labels = data.labels;
    }

    if (data.datasets) {
      chart.data.datasets = data.datasets;
    }

    chart.update();
    log(`Chart updated: ${id}`);
  };

  /**
   * Update chart dataset
   * @param {string} id - Chart ID
   * @param {number} datasetIndex - Dataset index
   * @param {Object} dataset - Dataset data
   */
  const updateDataset = (id, datasetIndex, dataset) => {
    const chart = getChart(id);
    if (!chart || !chart.data.datasets[datasetIndex]) return;

    Object.assign(chart.data.datasets[datasetIndex], dataset);
    chart.update();
    log(`Dataset updated: ${id}`);
  };

  /**
   * Add dataset ke chart
   * @param {string} id - Chart ID
   * @param {Object} dataset - Dataset to add
   */
  const addDataset = (id, dataset) => {
    const chart = getChart(id);
    if (!chart) return;

    chart.data.datasets.push(dataset);
    chart.update();
    log(`Dataset added to chart: ${id}`);
  };

  /**
   * Remove dataset dari chart
   * @param {string} id - Chart ID
   * @param {number} datasetIndex - Dataset index
   */
  const removeDataset = (id, datasetIndex) => {
    const chart = getChart(id);
    if (!chart || !chart.data.datasets[datasetIndex]) return;

    chart.data.datasets.splice(datasetIndex, 1);
    chart.update();
    log(`Dataset removed from chart: ${id}`);
  };

  /**
   * Destroy chart
   * @param {string} id - Chart ID
   */
  const destroyChart = (id) => {
    const chart = getChart(id);
    if (chart) {
      chart.destroy();
      chartInstances.delete(id);
      log(`Chart destroyed: ${id}`);
    }
  };

  /**
   * Destroy all charts
   */
  const destroyAllCharts = () => {
    chartInstances.forEach((chart) => {
      chart.destroy();
    });
    chartInstances.clear();
    log('All charts destroyed');
  };

  // ================================================================
  // PUBLIC API
  // ================================================================

  return {
    // Chart colors
    COLORS: CHART_COLORS,
    GRADIENTS: GRADIENT_COLORS,

    // Chart creation methods
    createLineChart,
    createBarChart,
    createDoughnutChart,
    createPieChart,
    createRadarChart,
    createAreaChart,

    // Chart management
    registerChart,
    getChart,
    updateChart,
    updateDataset,
    addDataset,
    removeDataset,
    destroyChart,
    destroyAllCharts,
  };
})();

// Log when Charts module is loaded
if (typeof window !== 'undefined') {
  if (typeof Chart === 'undefined') {
    console.warn('[CHARTS] Warning: Chart.js library not found. Please load Chart.js before this module.');
  } else {
    console.log('[CHARTS] Module loaded successfully');
  }
}
