import "./bootstrap";
import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

// Fungsi untuk menghitung waktu relatif (misal: 2 hours ago)
function timeAgo(dateStr) {
    const date = new Date(dateStr);
    const seconds = Math.floor((new Date() - date) / 1000);
    const intervals = [
        { label: "year", seconds: 31536000 },
        { label: "month", seconds: 2592000 },
        { label: "day", seconds: 86400 },
        { label: "hour", seconds: 3600 },
        { label: "minute", seconds: 60 },
        { label: "second", seconds: 1 },
    ];

    for (const interval of intervals) {
        const count = Math.floor(seconds / interval.seconds);
        if (count > 0) {
            return `${count} ${interval.label}${count !== 1 ? "s" : ""} ago`;
        }
    }
    return "just now";
}
window.timeAgo = timeAgo;

$(document).ready(function () {
    (function () {
        const placeholder =
            "https://placehold.co/200x200?text=Image+Placeholder";
        // Fungsi untuk menangani error gambar
        function handleImageError() {
            if (this.src !== placeholder) {
                this.src = placeholder;
            }
        }
        // Fungsi untuk menambahkan event listener ke gambar
        function addImageErrorListener(img) {
            img.addEventListener("error", handleImageError);

            // Periksa gambar yang sudah error sebelum event listener terpasang
            if (
                img.complete &&
                (img.naturalWidth === 0 || img.naturalHeight === 0)
            ) {
                img.src = placeholder;
            }
        }
        // Pasang listener ke semua gambar yang ada
        document.querySelectorAll("img").forEach(addImageErrorListener);
        // Observer untuk memantau penambahan gambar baru
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    // Cek node langsung yang merupakan gambar
                    if (node.tagName === "IMG") {
                        addImageErrorListener(node);
                    }
                    // Cek gambar di dalam subtree node yang ditambahkan
                    if (node.querySelectorAll) {
                        node.querySelectorAll("img").forEach(
                            addImageErrorListener,
                        );
                    }
                });
            });
        });
        // Mulai observasi perubahan DOM
        observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
        });
    })();
});
