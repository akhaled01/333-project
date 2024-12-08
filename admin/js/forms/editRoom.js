import { InvokeEditRoom } from "../api/rooms.js";
import { uploadFileToSupabase } from "../imageUpload.js";

/**
 * Handles the edit room form submission
 * @param {HTMLFormElement} form
 * @param {import("../types.mjs").Room} currentRoom
 * @returns {Promise<void>}
 */
export const editRoom = async (form, currentRoom) => {
  const formData = new FormData(form);
  
  // Get form values with type assertions
  const nameValue = formData.get("name");
  const capacityValue = formData.get("capacity");
  const equipmentValue = formData.get("equipment");
  // @ts-ignore
  const imageFile = form.querySelector('#edit-room-image')?.files[0];

  // Validate and convert form values
  const name = typeof nameValue === "string" ? nameValue.trim() : "";
  const capacity = capacityValue ? parseInt(String(capacityValue), 10) : 0;
  const equipment = typeof equipmentValue === "string" ? equipmentValue.trim() : "";

  // Validate required fields
  if (!name || !capacity || !equipment) {
    alert("Please fill in all required fields");
    return;
  }

  let image_url = currentRoom.image_url;

  // Upload new image if selected
  if (imageFile) {
    try {
      const submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) {
        // @ts-ignore
        submitButton.disabled = true;
        submitButton.textContent = 'Uploading Image...';
      }

      console.log('Uploading image file:', imageFile);
      image_url = await uploadFileToSupabase(imageFile);
      console.log('Uploaded image URL:', image_url);
    } catch (error) {
      console.error("Error uploading image:", error);
      alert("Failed to upload image. Please try again.");
      return;
    } finally {
      const submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) {
        // @ts-ignore
        submitButton.disabled = false;
        submitButton.textContent = 'Save Changes';
      }
    }
  }

  // Ensure room ID is a number
  const room = {
    id: parseInt(String(currentRoom.id), 10),
    name,
    capacity,
    equipment,
    image_url
  };

  console.log("Sending room data:", room); // Debug log

  try {
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      // @ts-ignore
      submitButton.disabled = true;
      submitButton.textContent = 'Saving...';
    }

    // @ts-ignore
    await InvokeEditRoom(room);
    
    // Reset image preview
    const imagePreview = document.getElementById('edit-image-preview');
    if (imagePreview) {
      imagePreview.classList.add('hidden');
      const previewImg = imagePreview.querySelector('img');
      if (previewImg) {
        previewImg.src = '';
      }
    }

    // Refresh the page to show updated data
    window.location.reload();
  } catch (error) {
    console.error("Error updating room:", error);
    alert("Failed to update room. Please try again.");
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      // @ts-ignore
      submitButton.disabled = false;
      submitButton.textContent = 'Save Changes';
    }
  }
};
