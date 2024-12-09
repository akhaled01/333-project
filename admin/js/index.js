import StatsCard from "./components/StatsCard.js";
import RoomManagement from "./components/RoomManagement.js";
import { fetchRooms } from "./api/rooms.js";
import { addRoom } from "./forms/addRoom.js";

// Function to fetch stats from API
async function fetchStats() {
  try {
    const [totalRooms, pendingBookings, totalUsers, todayBookings] = await Promise.all([
      fetch('./api/stats/rooms/total').then(r => r.json()),
      fetch('./api/stats/bookings/pending').then(r => r.json()),
      fetch('./api/stats/users/total').then(r => r.json()),
      fetch('./api/stats/bookings/today').then(r => r.json())
    ]);

    return {
      totalRooms: totalRooms.value,
      pendingBookings: pendingBookings.value,
      totalUsers: totalUsers.value,
      todayBookings: todayBookings.value
    };
  } catch (error) {
    console.error('Error fetching stats:', error);
    return {
      totalRooms: 0,
      pendingBookings: 0,
      totalUsers: 0,
      todayBookings: 0
    };
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  const statsContainer = document.getElementById("stats-container");
  const roomManagement = new RoomManagement("room-management");

  // Fetch rooms and stats
  const [rooms, stats] = await Promise.all([
    fetchRooms(),
    fetchStats()
  ]);

  // Create stats cards with real data
  const statsCards = [
    new StatsCard({
      icon: "fas fa-hotel",
      title: "Total Rooms",
      value: stats.totalRooms,
      color: "blue"
    }),
    new StatsCard({
      icon: "fas fa-clock",
      title: "Pending Bookings",
      value: stats.pendingBookings,
      color: "yellow"
    }),
    new StatsCard({
      icon: "fas fa-users",
      title: "Total Users",
      value: stats.totalUsers,
      color: "green"
    }),
    new StatsCard({
      icon: "fas fa-calendar-check",
      title: "Today's Bookings",
      value: stats.todayBookings,
      color: "purple"
    })
  ];

  // Render stats cards
  if (statsContainer) {
    statsContainer.innerHTML = statsCards.map((card) => card.render()).join("");
  }

  // Render room management
  if (rooms) {
    rooms.forEach((element) => {
      roomManagement.addRoom({
        id: element.id,
        name: element.name,
        capacity: element.capacity,
        equipment: element.equipment,
      });
    });
  }

  roomManagement.render();

  // Setup add room form handler
  const addRoomForm = document.getElementById("add-room-form");
  if (addRoomForm) {
    addRoomForm.addEventListener("submit", async (ev) => {
      ev.preventDefault();
      await addRoom(addRoomForm);
    });
  }
});
