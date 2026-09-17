<div class="space-y-4">
    <div id="camera-container" class="border rounded-lg p-4 bg-gray-50">
        <div class="text-center mb-4">
            <button 
                type="button" 
                id="start-camera" 
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
            >
                Start Camera
            </button>
            <button 
                type="button" 
                id="capture-photo" 
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors ml-2"
                style="display: none;"
            >
                Capture Photo
            </button>
        </div>
        
        <div id="video-container" class="text-center mb-4" style="display: none;">
            <video id="video" width="400" height="300" autoplay class="border rounded"></video>
        </div>
        
        <div id="photo-container" class="text-center" style="display: none;">
            <canvas id="canvas" width="400" height="300" class="border rounded"></canvas>
            <div class="mt-2">
                <button 
                    type="button" 
                    id="retake-photo" 
                    class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors"
                >
                    Retake Photo
                </button>
                <button 
                    type="button" 
                    id="use-photo" 
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors ml-2"
                >
                    Use This Photo
                </button>
            </div>
        </div>
    </div>
    
    <!-- Hidden input to store the captured photo data -->
    <input type="hidden" id="photo-data" name="photo_data" />
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const startCameraBtn = document.getElementById('start-camera');
    const capturePhotoBtn = document.getElementById('capture-photo');
    const retakePhotoBtn = document.getElementById('retake-photo');
    const usePhotoBtn = document.getElementById('use-photo');
    const photoDataInput = document.getElementById('photo-data');
    
    const videoContainer = document.getElementById('video-container');
    const photoContainer = document.getElementById('photo-container');
    
    let stream = null;
    
    startCameraBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'user' // Use front camera
                } 
            });
            video.srcObject = stream;
            
            videoContainer.style.display = 'block';
            capturePhotoBtn.style.display = 'inline-block';
            startCameraBtn.style.display = 'none';
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert('Unable to access camera. Please check permissions.');
        }
    });
    
    capturePhotoBtn.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert canvas to blob and then to base64
canvas.toBlob(function(blob) {
    const reader = new FileReader();
    reader.onload = function(e) {
        photoDataInput.value = e.target.result;

        videoContainer.style.display = 'none';
        photoContainer.style.display = 'block';
        capturePhotoBtn.style.display = 'none';

        // Stop camera stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    };
    reader.readAsDataURL(blob);
}, 'image/jpeg', 0.8);

        
        videoContainer.style.display = 'none';
        photoContainer.style.display = 'block';
        capturePhotoBtn.style.display = 'none';
        
        // Stop camera stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
    
usePhotoBtn.addEventListener('click', function() {
    if (photoDataInput.value) {
        document.getElementById('final-submit').click();
    }
});

    
    usePhotoBtn.addEventListener('click', function() {
        if (photoDataInput.value) {
           
            const event = new CustomEvent('photoCapture', { 
                detail: { photoData: photoDataInput.value } 
            });
            document.dispatchEvent(event);
            
            alert('Photo captured successfully!');
        }
    });
});
</script>