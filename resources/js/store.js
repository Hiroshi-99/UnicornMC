document.addEventListener("DOMContentLoaded", () => {
    const categoryButtons = document.querySelectorAll(".category-btn");
    const storeSections = document.querySelectorAll(".store-section");

    categoryButtons.forEach((button) => {
        button.addEventListener("click", () => {
            // Remove active class from all buttons and sections
            categoryButtons.forEach((btn) => btn.classList.remove("active"));
            storeSections.forEach((section) =>
                section.classList.remove("active")
            );

            // Add active class to clicked button and corresponding section
            button.classList.add("active");
            const category = button.dataset.category;
            document
                .querySelector(`.store-section[data-category="${category}"]`)
                .classList.add("active");
        });
    });

    // Initialize particles
    initParticles();
});

// Particle initialization function
async function initParticles() {
    await tsParticles.load("tsparticles", {
        fullScreen: {
            enable: true,
            zIndex: 1,
        },
        particles: {
            number: {
                value: 60,
                density: {
                    enable: true,
                    value_area: 900,
                },
            },
            color: {
                value: ["#ff00ff", "#da70d6", "#ee82ee", "#ff69b4", "#dda0dd"],
            },
            shape: {
                type: "circle",
            },
            opacity: {
                value: 0.6,
                random: true,
                animation: {
                    enable: true,
                    speed: 0.5,
                    minimumValue: 0.1,
                    sync: false,
                },
            },
            size: {
                value: 4,
                random: true,
                animation: {
                    enable: true,
                    speed: 1,
                    minimumValue: 0.1,
                    sync: false,
                },
            },
            move: {
                enable: true,
                speed: 0.8,
                direction: "none",
                random: true,
                straight: false,
                outModes: {
                    default: "bounce",
                },
                attract: {
                    enable: true,
                    rotateX: 600,
                    rotateY: 1200,
                },
            },
            links: {
                enable: true,
                distance: 150,
                color: "#ff69b4",
                opacity: 0.2,
                width: 1,
            },
        },
        interactivity: {
            detectsOn: "window",
            events: {
                onHover: {
                    enable: true,
                    mode: "bubble",
                },
                onClick: {
                    enable: true,
                    mode: "repulse",
                },
                resize: true,
            },
            modes: {
                bubble: {
                    distance: 200,
                    size: 6,
                    duration: 0.3,
                    opacity: 0.8,
                },
                repulse: {
                    distance: 200,
                    duration: 0.4,
                },
            },
        },
        background: {
            color: "transparent",
        },
        detectRetina: true,
    });
}
