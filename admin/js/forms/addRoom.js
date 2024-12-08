// @ts-nocheck
import { InvokeCreateRoom } from "../api/rooms.js";
import { uploadFileToSupabase } from "../imageUpload.js";
import CustomDialog from "../components/dialog.js";

/**
 *
 * @param {HTMLElement | null} formContainer
 */
export const addRoom = async (formContainer) => {
  /**
   * @type {CustomDialog | null}
   */
  const dialog = document.querySelector("custom-dialog#add-room-dialog");

  if (formContainer && dialog) {
    // @ts-ignore
    const name = document.getElementById("room-name").value;
    const capacity = parseInt(
      // @ts-ignore
      document.getElementById("room-capacity").value,
      10
    );
    // @ts-ignore
    const equipment = document.getElementById("room-equipment").value;
    // @ts-ignore
    const imageFile = document.getElementById("room-image").files[0];

    if (!name || !capacity || !equipment) {
      alert("Please fill in all required fields.");
      return;
    }

    try {
      let imageUrl = null;
      const submitButton = formContainer.querySelector('button[type="submit"]');
      
      // Upload image if one is selected
      if (imageFile) {
        try {
          if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Uploading...';
          }

          imageUrl = await uploadFileToSupabase(imageFile);
        } catch (error) {
          console.error("Error uploading image:", error);
          alert("Failed to upload image. Please try again.");
          return;
        }
      }

      // Create room with image URL
      if (submitButton) {
        submitButton.textContent = 'Creating Room...';
      }

      const response = await InvokeCreateRoom({
        name,
        capacity,
        equipment,
        image_url: imageUrl
      });

      if (response.success) {
        // Reset form and close dialog
        formContainer.reset();
        const preview = document.getElementById('image-preview');
        if (preview) {
          preview.classList.add('hidden');
        }
        dialog.close();
        
        // Reload the page to show the new room
        window.location.reload();
      } else {
        throw new Error(response.message || "Failed to create room");
      }
    } catch (error) {
      console.error("Error:", error);
      alert(error.message || "An error occurred. Please try again.");
    } finally {
      const submitButton = formContainer.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = 'Add Room';
      }
    }
  }
};
