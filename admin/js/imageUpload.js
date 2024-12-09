// @ts-nocheck
// Function to preview the selected image
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = preview.querySelector('img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '';
        preview.classList.add('hidden');
    }
}

async function getSupabaseCredentials() {
    try {
        const response = await fetch("./api/load-env/index.php");
        if (!response.ok) {
            throw new Error("Failed to load environment variables");
        }
        return await response.json();
    } catch (error) {
        console.error("Error fetching Supabase credentials:", error);
        throw error;
    }
}

/**
 * Upload file to Supabase storage
 * @param {File} file 
 * @returns {Promise<string>} The URL of the uploaded file
 */
async function uploadFileToSupabase(file) {
    if (!file) {
        throw new Error("No file provided");
    }

    const { supabaseUrl, supabaseKey, bucketName } = await getSupabaseCredentials();
    
    // Generate a unique filename
    const timestamp = new Date().getTime();
    const randomString = Math.random().toString(36).substring(2, 15);
    const extension = file.name.split('.').pop();
    const fileName = `rooms/${timestamp}-${randomString}.${extension}`;

    try {
        // Upload to Supabase Storage
        const uploadUrl = `${supabaseUrl}/storage/v1/object/${bucketName}/${fileName}`;
        
        const formData = new FormData();
        formData.append('file', file);

        const uploadResponse = await fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${supabaseKey}`,
            },
            body: formData
        });

        if (!uploadResponse.ok) {
            const errorText = await uploadResponse.text();
            console.error('Upload response:', errorText);
            throw new Error('Failed to upload file to Supabase');
        }

        // Return the public URL
        return `${supabaseUrl}/storage/v1/object/public/${bucketName}/${fileName}`;
    } catch (error) {
        console.error('Error uploading to Supabase:', error);
        throw new Error('Failed to upload image');
    }
}

export { previewImage, uploadFileToSupabase };
