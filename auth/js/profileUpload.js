// Function to preview the selected image
export function previewImage(input) {
    const preview = document.getElementById('preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

export async function getSupabaseCredentials() {
    try {
        const response = await fetch("./api/load-env/index.php");
        if (!response.ok) {
            throw new Error("Failed to load environment variables.");
        }
        const credentials = await response.json();
        return credentials;
    } catch (error) {
        console.error("Error fetching Supabase credentials:", error.message);
        throw error;
    }
}

export async function uploadProfilePicture(file) {
    try {
        const { supabaseUrl, supabaseKey, bucketName } = await getSupabaseCredentials();

        if (!file) {
            throw new Error("No file provided.");
        }

        const fileName = `profile-${Date.now()}-${encodeURIComponent(file.name)}`;
        const uploadUrl = `${supabaseUrl}/storage/v1/object/${bucketName}/${fileName}`;

        const response = await fetch(uploadUrl, {
            method: "POST",
            headers: {
                Authorization: `Bearer ${supabaseKey}`,
                "Content-Type": file.type,
            },
            body: file,
        });

        if (response.ok) {
            const publicUrl = `${supabaseUrl}/storage/v1/object/public/${bucketName}/${fileName}`;
            return publicUrl;
        } else {
            const error = await response.text();
            throw new Error(`Upload failed: ${error}`);
        }
    } catch (error) {
        console.error("Error uploading profile picture:", error.message);
        throw error;
    }
}
