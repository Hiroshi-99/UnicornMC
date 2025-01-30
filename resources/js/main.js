// Set to false if you do not want the 'firefly' particles to display in the background
const doParticles = true;

// Configuration settings
const config = {
    serverOfflineText: "Server isn't online!",
    ipCopiedText: "IP copied!",
    tellUsersWhenServerIsOffline: true,
};

// Initialize tsParticles
window.addEventListener("DOMContentLoaded", async () => {
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
});

// Initialize click-to-copy functionality
document.addEventListener("DOMContentLoaded", () => {
    const ipElement = document.getElementById("ip");
    if (ipElement) {
        const originalText = ipElement.textContent;

        ipElement.addEventListener("click", () => {
            navigator.clipboard
                .writeText(originalText)
                .then(() => {
                    ipElement.textContent = "IP copied!";
                    setTimeout(() => {
                        ipElement.textContent = originalText;
                    }, 800);
                })
                .catch((err) => {
                    console.error("Failed to copy:", err);
                });
        });
    }
});

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Player count functionality
    const updatePlayerCount = async () => {
        const playerCountElement = document.getElementById("sip");
        const playerCountBanner = document.getElementById("playercount");

        if (!playerCountElement) return;

        const serverIP = playerCountElement.getAttribute("data-ip");

        try {
            const response = await fetch(
                `https://api.mcsrvstat.us/3/${serverIP}`
            );
            const data = await response.json();

            if (data.online) {
                playerCountElement.textContent = data.players.online;
                playerCountElement.style.display = "inline";
            } else if (config.tellUsersWhenServerIsOffline) {
                playerCountBanner.textContent = config.serverOfflineText;
            } else {
                playerCountElement.style.display = "none";
            }
        } catch (error) {
            console.error("Error fetching player count:", error);
            playerCountElement.style.display = "none";
        }
    };

    // Initial player count update
    updatePlayerCount();

    // Update player count every minute
    setInterval(updatePlayerCount, 60000);
});
