<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview - {{ $attachment->file_original_name }}</title>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #111; overflow-x: hidden; user-select: none; -webkit-user-select: none; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; pointer-events: auto; background: transparent; }
        
        /* For Images */
        .image-container { width: 100%; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .image-container img { max-width: 100%; max-height: 100%; object-fit: contain; pointer-events: none; }
        
        /* For PDF.js */
        #pdf-viewer-container { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 20px 0; overflow-y: auto; height: 100vh; }
        canvas { max-width: 100%; height: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.5); pointer-events: none; }
        
        /* Loading */
        #loading { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-family: sans-serif; }
    </style>
</head>
<body oncontextmenu="return false;">

    @php
        $ext = strtolower($attachment->file_type ?? '');
        $rawUrl = route('soal.file', ['token' => $soal->unique_token, 'attachment' => $attachment->id]);
        if($isAdminPreview) $rawUrl .= '?admin_preview=1';
    @endphp

    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
        <div class="overlay" oncontextmenu="return false;"></div>
        <div class="image-container">
            <img src="{{ $rawUrl }}" alt="Preview" draggable="false">
        </div>
    @else
        <div id="loading">Memuat PDF secara aman...</div>
        <div id="pdf-viewer-container"></div>
        <div class="overlay" oncontextmenu="return false;" style="position: fixed; pointer-events: none;"></div>

        <!-- PDF.js via CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const loadingTask = pdfjsLib.getDocument('{{ $rawUrl }}');
            loadingTask.promise.then(function(pdf) {
                document.getElementById('loading').style.display = 'none';
                const viewer = document.getElementById('pdf-viewer-container');
                
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        const baseScale = 1.5;
                        const outputScale = window.devicePixelRatio || 1;
                        const viewport = page.getViewport({scale: baseScale});
                        
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        
                        canvas.width = Math.floor(viewport.width * outputScale);
                        canvas.height = Math.floor(viewport.height * outputScale);
                        canvas.style.width = Math.floor(viewport.width) + "px";
                        canvas.style.height = Math.floor(viewport.height) + "px";
                        
                        const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;
                        
                        // Prevent context menu on canvas
                        canvas.oncontextmenu = function() { return false; };
                        
                        viewer.appendChild(canvas);
                        
                        const renderContext = {
                            canvasContext: context,
                            transform: transform,
                            viewport: viewport
                        };
                        page.render(renderContext);
                    });
                }
            }).catch(function(error) {
                document.getElementById('loading').innerText = 'Gagal memuat PDF.';
            });
        </script>
    @endif

    <script>
        // Total protection block
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('dragstart', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                alert('Tangkapan layar dinonaktifkan demi keamanan!');
                document.body.style.opacity = '0';
                setTimeout(() => document.body.style.opacity = '1', 1000);
            }
            if ((e.ctrlKey || e.metaKey) && ['s', 'p', 'c', 'u', 'a'].includes(e.key.toLowerCase())) {
                e.preventDefault();
            }
        });
        document.addEventListener('keyup', e => {
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
            }
        });

        @if(!isset($isAdminPreview) || !$isAdminPreview)
        setInterval(() => {
            fetch('{{ route('soal.status', $soal->unique_token) }}')
                .then(res => res.json())
                .then(data => {
                    if (data.access_status !== 'active') {
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Status check failed', err));
        }, 5000);
        @endif
    </script>
</body>
</html>
