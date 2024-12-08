export default class StatsCard extends HTMLElement {
  /**
   * @param {Object} options
   * @param {string} options.icon - Icon class name
   * @param {string} options.title - Card title
   * @param {number|string} options.value - Card value
   * @param {string} options.color - Color theme for the card
   */
  constructor(options) {
    super();
    this.icon = options.icon;
    this.title = options.title;
    this.value = options.value;
    this.color = options.color;
  }

  connectedCallback() {
    this.innerHTML = this.render();
  }

  render() {
    const colorClasses = {
      blue: 'bg-blue-900/50 text-blue-400',
      green: 'bg-green-900/50 text-green-400',
      yellow: 'bg-yellow-900/50 text-yellow-400',
      purple: 'bg-purple-900/50 text-purple-400'
    };

    // @ts-ignore
    const colorClass = colorClasses[this.color] || colorClasses.blue;

    return `
      <div class="bg-zinc-800 rounded-lg p-6 shadow-sm border border-zinc-700">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center">
            <div class="rounded-full w-12 h-12 flex items-center justify-center ${colorClass}">
              <i class="${this.icon} text-xl"></i>
            </div>
            <h3 class="ml-4 text-lg font-semibold text-zinc-100">${this.title}</h3>
          </div>
        </div>
        <div class="text-3xl font-bold text-zinc-100">${this.value}</div>
      </div>
    `;
  }
}

customElements.define("stats-card", StatsCard);
