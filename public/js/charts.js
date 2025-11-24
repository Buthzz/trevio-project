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
 * ===================================================================
 */

'use strict';

const Charts = (function () {
  let chartInstances = new Map();

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

  const log = (message) => {
    if (window.DEBUG) console.log(`[CHARTS] ${message}`);
  };

  const logError = (message, error = null) => {
    if (window.DEBUG) console.error(`[CHARTS ERROR] ${message}`, error || '');
  };

  const getCanvasElement = (canvas) => {
    if (canvas instanceof HTMLCanvasElement) return canvas;
    return document.querySelector(canvas);
  };

  const createGradient = (canvas, colors) => {
    if (!canvas || !colors || !Array.isArray(colors) || colors.length < 2) {
      return (colors && colors[0]) || '#4f46e5';
    }
    
    try {
      const ctx = canvas.getContext('2d');
      if (!ctx) {
        log('Could not get 2D context from canvas');
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

  const createLineChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) { logError('Canvas element not found'); return null; }
    if (typeof Chart === 'undefined') { logError('Chart.js library not loaded'); return null; }

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
              ...dataset,
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
      return chart;
    } catch (e) {
      logError('Failed to create line chart', e);
      return null;
    }
  };

  const createBarChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) { logError('Canvas element not found'); return null; }
    if (typeof Chart === 'undefined') { logError('Chart.js library not loaded'); return null; }

    try {
      const chartConfig = {
        type: 'bar',
        data: {
          labels: data.labels || [],
          datasets: (data.datasets || []).map((dataset, index) => {
            const colors = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor : [dataset.backgroundColor || CHART_COLORS.primary];
            return {
              label: dataset.label || `Dataset ${index + 1}`,
              data: dataset.data || [],
              backgroundColor: colors,
              borderColor: colors.map((c) => (typeof c === 'string' ? c.replace('0.6', '1') : c)),
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
      return chart;
    } catch (e) {
      logError('Failed to create bar chart', e);
      return null;
    }
  };

  const createDoughnutChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) { logError('Canvas element not found'); return null; }
    if (typeof Chart === 'undefined') { logError('Chart.js library not loaded'); return null; }

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
    } catch (e) {
      logError('Failed to create doughnut chart', e);
      return null;
    }
  };

  const createPieChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) { logError('Canvas element not found'); return null; }
    if (typeof Chart === 'undefined') { logError('Chart.js library not loaded'); return null; }

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
    } catch (e) {
      logError('Failed to create pie chart', e);
      return null;
    }
  };

  const createRadarChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) { logError('Canvas element not found'); return null; }
    if (typeof Chart === 'undefined') { logError('Chart.js library not loaded'); return null; }

    try {
      const chartConfig = {
        type: 'radar',
        data: {
          labels: data.labels || [],
          datasets: (data.datasets || []).map((dataset, index) => {
            const color = dataset.borderColor || CHART_COLORS.primary;
            let bgColor = color;
            try {
              if (typeof color === 'string') {
                if (color.includes('rgba')) {
                  bgColor = color.replace(/,[^,]*\)$/, ', 0.1)');
                } else if (color.includes('rgb')) {
                  bgColor = color.replace(/\)$/, ', 0.1)').replace('rgb', 'rgba');
                }
              }
            } catch (e) {
              bgColor = color;
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
      return chart;
    } catch (e) {
      logError('Failed to create radar chart', e);
      return null;
    }
  };

  const createAreaChart = (canvas, data, options = {}) => {
    const canvasEl = getCanvasElement(canvas);
    if (!canvasEl) { logError('Canvas element not found'); return null; }
    if (typeof Chart === 'undefined') { logError('Chart.js library not loaded'); return null; }

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
              displayColors: true,
              callbacks: options.tooltipCallbacks || {},
            },
          },
          scales: {
            y: {
              beginAtZero: options.beginAtZero !== false,
              stacked: options.stacked || false,
              grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' },
              ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8' },
              ...options.yScale,
            },
            x: {
              stacked: options.stacked || false,
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
      return chart;
    } catch (e) {
      logError('Failed to create area chart', e);
      return null;
    }
  };

  const registerChart = (id, chart) => {
    if (!id || typeof id !== 'string') { logError('Invalid chart ID'); return; }
    if (chartInstances.has(id)) {
      try { destroyChart(id); } catch (e) { logError(`Failed to destroy existing chart: ${id}`, e); }
    }
    chartInstances.set(id, chart);
    log(`Chart registered: ${id}`);
  };

  const getChart = (id) => chartInstances.get(id) || null;

  const updateChart = (id, data) => {
    if (!data || typeof data !== 'object') { logError('Invalid data provided to updateChart'); return; }
    const chart = getChart(id);
    if (!chart) { logError(`Chart not found: ${id}`); return; }
    try {
      if (Array.isArray(data.labels)) chart.data.labels = data.labels;
      if (Array.isArray(data.datasets)) chart.data.datasets = data.datasets;
      chart.update('none');
      log(`Chart updated: ${id}`);
    } catch (e) {
      logError(`Failed to update chart: ${id}`, e);
    }
  };

  const updateDataset = (id, datasetIndex, dataset) => {
    const chart = getChart(id);
    if (!chart || !chart.data.datasets[datasetIndex]) { logError(`Invalid chart or dataset index: ${id}`); return; }
    try {
      Object.assign(chart.data.datasets[datasetIndex], dataset);
      chart.update('none');
      log(`Dataset updated: ${id}`);
    } catch (e) {
      logError(`Failed to update dataset: ${id}`, e);
    }
  };

  const addDataset = (id, dataset) => {
    const chart = getChart(id);
    if (!chart) { logError(`Chart not found: ${id}`); return; }
    try {
      chart.data.datasets.push(dataset);
      chart.update('none');
      log(`Dataset added to chart: ${id}`);
    } catch (e) {
      logError(`Failed to add dataset to chart: ${id}`, e);
    }
  };

  const removeDataset = (id, datasetIndex) => {
    const chart = getChart(id);
    if (!chart || !chart.data.datasets[datasetIndex]) { logError(`Invalid chart or dataset index: ${id}`); return; }
    try {
      chart.data.datasets.splice(datasetIndex, 1);
      chart.update('none');
      log(`Dataset removed from chart: ${id}`);
    } catch (e) {
      logError(`Failed to remove dataset from chart: ${id}`, e);
    }
  };

  const destroyChart = (id) => {
    const chart = getChart(id);
    if (chart) {
      try {
        chart.destroy();
        chartInstances.delete(id);
        log(`Chart destroyed: ${id}`);
      } catch (e) {
        logError(`Failed to destroy chart: ${id}`, e);
      }
    }
  };

  const destroyAllCharts = () => {
    try {
      chartInstances.forEach((chart) => { chart.destroy(); });
      chartInstances.clear();
      log('All charts destroyed');
    } catch (e) {
      logError('Failed to destroy all charts', e);
    }
  };

  return {
    COLORS: CHART_COLORS,
    GRADIENTS: GRADIENT_COLORS,
    createLineChart, createBarChart, createDoughnutChart, createPieChart, createRadarChart, createAreaChart,
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
