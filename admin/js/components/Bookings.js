export class Bookings extends HTMLElement {
  constructor() {
    super();
    /**
     * @type {any[]}
     */
    this.bookings = [];
  }

  async connectedCallback() {
    await this.fetchBookings();
    this.render();
    this.attachEventListeners();
  }

  async fetchBookings() {
    try {
      const response = await fetch("./api/bookings");
      const data = await response.json();
      console.log(data);
      if (data.success) {
        this.bookings = data.data;
      }
    } catch (error) {
      console.error("Failed to fetch bookings:", error);
    }
  }

  /**
   *
   * @param {string} bookingId
   * @param {string} status
   */
  async updateBookingStatus(bookingId, status) {
    try {
      const response = await fetch("./api/bookings/update-status", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ booking_id: bookingId, status }),
      });
      const data = await response.json();
      if (data.success) {
        await this.fetchBookings();
        window.location.reload();
      }
    } catch (error) {
      console.error("Failed to update booking status:", error);
    }
  }

  /**
   *
   * @param {string} status
   * @returns
   */
  getStatusBadgeClass(status) {
    console.log(status);
    const baseClasses =
      "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium";
    switch (status) {
      case "pending":
        return `${baseClasses} bg-yellow-900/50 text-yellow-400`;
      case "confirmed":
        return `${baseClasses} bg-green-900/50 text-green-400`;
      case "cancelled":
        return `${baseClasses} bg-red-900/50 text-red-400`;
      default:
        return `${baseClasses} bg-zinc-800/50 text-zinc-400`;
    }
  }

  /**
   *
   * @param {string} date
   * @param {string} time
   * @returns
   */
  formatDateTime(date, time) {
    const dateObj = new Date(`${date}T${time}`);
    return new Intl.DateTimeFormat("en-US", {
      dateStyle: "medium",
      timeStyle: "short",
    }).format(dateObj);
  }

  render() {
    this.innerHTML = `
            <div class="relative overflow-hidden rounded-lg border border-zinc-700 bg-zinc-800 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-zinc-700">
                        <thead>
                            <tr class="bg-zinc-800/50">
                                <th scope="col" class="w-1/4 px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-400">User</th>
                                <th scope="col" class="w-1/6 px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-400">Room</th>
                                <th scope="col" class="w-1/4 px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-400">Date & Time</th>
                                <th scope="col" class="w-1/6 px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-400">Status</th>
                                <th scope="col" class="w-1/6 px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-700 bg-zinc-800">
                            ${
                              this.bookings.length === 0
                                ? `
                              <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-400">
                                  <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fas fa-calendar-times text-zinc-500 text-4xl mb-2"></i>
                                    <p>No bookings available</p>
                                    <p class="text-xs text-zinc-500">Bookings will appear here when users make reservations</p>
                                  </div>
                                </td>
                              </tr>
                            `
                                : this.bookings
                                    .map(
                                      (booking) => `
                                <tr class="hover:bg-zinc-700/30 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 flex-shrink-0 rounded-full bg-zinc-700/50 flex items-center justify-center">
                                                <i class="fas fa-user text-zinc-400"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-zinc-100">${
                                                  booking.user_name
                                                }</div>
                                                <div class="text-xs text-zinc-400">${
                                                  booking.user_email
                                                }</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-door-open text-blue-400 mr-2"></i>
                                            <span class="text-sm text-zinc-100">${
                                              booking.room_name
                                            }</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-clock text-blue-400 mr-2"></i>
                                            <span class="text-sm text-zinc-100">${this.formatDateTime(
                                              booking.date,
                                              booking.time
                                            )}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="${this.getStatusBadgeClass(
                                          booking.status
                                        )}">
                                            ${
                                              booking.status
                                                .charAt(0)
                                                .toUpperCase() +
                                              booking.status.slice(1)
                                            }
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        ${
                                          booking.status === "pending"
                                            ? `
                                            <div class="inline-flex -space-x-px">
                                                <button 
                                                    data-booking-id="${booking.booking_id}" 
                                                    data-action="confirm"
                                                    class="inline-flex items-center rounded-l-md bg-green-900/50 px-2.5 py-1.5 text-sm font-medium text-green-400 hover:bg-green-900/70 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-zinc-800 transition-colors">
                                                    <i class="fas fa-check h-3.5 w-3.5"></i>
                                                </button>
                                                <button 
                                                    data-booking-id="${booking.booking_id}" 
                                                    data-action="cancel"
                                                    class="inline-flex items-center rounded-r-md bg-red-900/50 px-2.5 py-1.5 text-sm font-medium text-red-400 hover:bg-red-900/70 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-800 transition-colors">
                                                    <i class="fas fa-times h-3.5 w-3.5"></i>
                                                </button>
                                            </div>
                                            `
                                            : "-"
                                        }
                                    </td>
                                </tr>
                            `
                                    )
                                    .join("")
                            }
                        </tbody>
                    </table>
                </div>
            </div>
        `;
  }

  attachEventListeners() {
    this.querySelectorAll("button[data-booking-id]").forEach((button) => {
      button.addEventListener("click", (e) => {
        // @ts-ignore
        if (e.target && e.target.dataset) {
          // @ts-ignore
          const bookingId = e.target.dataset.bookingId;
          // @ts-ignore
          const action = e.target.dataset.action;
          const status = action === "confirm" ? "confirmed" : "cancelled";
          this.updateBookingStatus(bookingId, status);
        }
      });
    });
  }
}

customElements.define("bookings-table", Bookings);
