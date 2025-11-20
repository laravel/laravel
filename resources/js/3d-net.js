// 3D Net Background with Three.js
import * as THREE from 'three';

export function init3DNetBackground() {
    const container = document.getElementById('canvas-container');
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });

    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);

    // Create grid/net geometry
    const gridSize = 20;
    const gridDivisions = 20;
    const vertices = [];
    const indices = [];

    // Generate grid vertices
    for (let i = 0; i <= gridDivisions; i++) {
        for (let j = 0; j <= gridDivisions; j++) {
            const x = (i / gridDivisions - 0.5) * gridSize;
            const y = (j / gridDivisions - 0.5) * gridSize;
            const z = 0;
            vertices.push(x, y, z);
        }
    }

    // Generate indices for lines
    for (let i = 0; i < gridDivisions; i++) {
        for (let j = 0; j < gridDivisions; j++) {
            const a = i * (gridDivisions + 1) + j;
            const b = a + 1;
            const c = a + (gridDivisions + 1);
            const d = c + 1;

            // Horizontal lines
            indices.push(a, b);
            // Vertical lines
            indices.push(a, c);
        }
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));
    geometry.setIndex(indices);

    // Get theme-aware color
    const getThemeColor = () => {
        const theme = document.documentElement.getAttribute('data-theme');
        return theme === 'light' ? 0x3b82f6 : 0x60a5fa; // blue-500 : blue-400
    };

    const material = new THREE.LineBasicMaterial({
        color: getThemeColor(),
        transparent: true,
        opacity: 0.3,
    });

    const grid = new THREE.LineSegments(geometry, material);
    scene.add(grid);

    camera.position.z = 8;

    // Mouse interaction
    let mouseX = 0;
    let mouseY = 0;
    let targetRotationX = 0;
    let targetRotationY = 0;

    document.addEventListener('mousemove', (event) => {
        mouseX = (event.clientX / window.innerWidth - 0.5) * 2;
        mouseY = (event.clientY / window.innerHeight - 0.5) * 2;

        targetRotationX = mouseY * 0.3;
        targetRotationY = mouseX * 0.3;
    });

    // Animation
    const clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);

        const elapsedTime = clock.getElapsedTime();
        const positions = geometry.attributes.position.array;

        // Wave effect
        for (let i = 0; i <= gridDivisions; i++) {
            for (let j = 0; j <= gridDivisions; j++) {
                const index = (i * (gridDivisions + 1) + j) * 3;
                const x = positions[index];
                const y = positions[index + 1];

                // Create wave based on mouse position and time
                const distanceFromMouse = Math.sqrt(
                    Math.pow(x / gridSize + mouseX, 2) +
                    Math.pow(y / gridSize + mouseY, 2)
                );

                positions[index + 2] = Math.sin(elapsedTime * 2 + distanceFromMouse * 5) * 0.5;
            }
        }

        geometry.attributes.position.needsUpdate = true;

        // Smooth rotation towards mouse
        grid.rotation.x += (targetRotationX - grid.rotation.x) * 0.05;
        grid.rotation.y += (targetRotationY - grid.rotation.y) * 0.05;

        // Slow ambient rotation
        grid.rotation.z = Math.sin(elapsedTime * 0.1) * 0.1;

        renderer.render(scene, camera);
    }

    animate();

    // Handle window resize
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    });

    // Handle theme changes
    const observer = new MutationObserver(() => {
        material.color.setHex(getThemeColor());
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });

    return { scene, camera, renderer, grid };
}
