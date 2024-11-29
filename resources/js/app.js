import './bootstrap';
import 'flowbite';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid'
import './../../vendor/power-components/livewire-powergrid/dist/tailwind.css'

// DARK MODE TOGGLE BUTTON
const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");
const themeToggleBtn = document.getElementById("theme-toggle");


const isDarkMode = () =>
    localStorage.getItem("color-theme") === "dark";

const setTheme = (isDark) => {
    document.documentElement.classList.toggle("dark", isDark);
    localStorage.setItem("color-theme", isDark ? "dark" : "light");
    updateThemeIcon(isDark);
};

const updateThemeIcon = (isDark) => {
    themeToggleDarkIcon.classList.toggle("hidden", !isDark);
    themeToggleLightIcon.classList.toggle("hidden", isDark);
};

// Initialize theme
const initializeTheme = () => {
    const storedTheme = localStorage.getItem("color-theme");
    const isDark = storedTheme === "dark" || (!storedTheme && window.matchMedia("(prefers-color-scheme: dark)").matches);
    setTheme(isDark);
};

// Set initial theme when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeTheme);
// Toggle theme on button click
themeToggleBtn.addEventListener("click", () => {
    const newDarkMode = !isDarkMode();
    setTheme(newDarkMode);
});
