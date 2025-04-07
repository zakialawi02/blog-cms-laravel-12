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
    const themeToggle = document.getElementById("theme-toggle");
    const iconSun = document.getElementById("icon-sun");
    const iconMoon = document.getElementById("icon-moon");
    function applyTheme(theme) {
        document.documentElement.classList.toggle("dark", theme === "dark");
        localStorage.setItem("theme", theme);
        iconSun.classList.toggle("hidden", theme !== "dark");
        iconMoon.classList.toggle("hidden", theme === "dark");
    }
    // Cek tema saat ini
    const savedTheme = localStorage.getItem("theme") || "light";
    applyTheme(savedTheme);
    // Toggle tema saat tombol diklik
    themeToggle.addEventListener("click", function () {
        const newTheme = document.documentElement.classList.contains("dark")
            ? "light"
            : "dark";
        applyTheme(newTheme);
    });

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
        setTimeout(() => {
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
        }, 2000);
    })();
});
