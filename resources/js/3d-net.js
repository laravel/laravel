// 3D Neon Circuit Board Background - Enhanced
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

    // Get theme-aware neon colors
    const getThemeColors = () => {
        const theme = document.documentElement.getAttribute('data-theme');
        return theme === 'light'
            ? {
                primary: 0x3b82f6,    // Blue-500 (soft blue)
                secondary: 0x8b5cf6,  // Violet-500 (soft purple)
                accent: 0x14b8a6,     // Teal-500 (soft teal)
                inactive: 0xe5e7eb,   // Gray-200 (very light gray)
            }
            : {
                primary: 0x00ccff,    // Light Blue
                secondary: 0xff0099,  // Pink
                accent: 0x00ff88,     // Teal
                inactive: 0x666666,   // Dark Gray
            };
    };

    const colors = getThemeColors();
    const circuitLines = [];
    const nodes = [];

    // Create more extensive circuit board pattern for full coverage
    const gridSize = 50;  // Larger grid for full coverage
    const spacing = 1.5;  // Denser spacing

    // Generate horizontal and vertical traces
    for (let i = -gridSize / 2; i < gridSize / 2; i += spacing) {
        // Horizontal traces
        if (Math.random() > 0.2) {  // More traces
            const points = [];
            const y = i + (Math.random() - 0.5) * 0.3;
            const startX = -gridSize / 2;
            const endX = gridSize / 2;

            let currentX = startX;
            points.push(new THREE.Vector3(currentX, y, 0));

            while (currentX < endX) {
                currentX += Math.random() * 2 + 0.5;
                if (Math.random() > 0.75) {
                    const verticalOffset = (Math.random() - 0.5) * 1.5;
                    points.push(new THREE.Vector3(currentX, y + verticalOffset, 0));
                } else {
                    points.push(new THREE.Vector3(currentX, y, 0));
                }
            }

            const geometry = new THREE.BufferGeometry().setFromPoints(points);
            const neonColor = Math.random() > 0.5 ? colors.primary : colors.accent;
            const material = new THREE.LineBasicMaterial({
                color: colors.inactive,  // Start with gray
                transparent: true,
                opacity: 0.3,
                linewidth: 1,
            });

            const line = new THREE.Line(geometry, material);
            line.userData = {
                baseOpacity: 0.3,
                neonColor: neonColor,
                inactiveColor: colors.inactive,
                glowIntensity: 0,
                originalPositions: new Float32Array(geometry.attributes.position.array)
            };
            circuitLines.push(line);
            scene.add(line);

            // Add connection nodes
            points.forEach((point, idx) => {
                if (idx % 4 === 0) {
                    const nodeGeometry = new THREE.SphereGeometry(0.08, 6, 6);
                    const nodeMaterial = new THREE.MeshBasicMaterial({
                        color: colors.inactive,
                        transparent: true,
                        opacity: 0.4,
                    });
                    const node = new THREE.Mesh(nodeGeometry, nodeMaterial);
                    node.position.copy(point);
                    node.userData = {
                        baseOpacity: 0.4,
                        glowIntensity: 0,
                        neonColor: neonColor,
                        inactiveColor: colors.inactive,
                        originalPosition: point.clone()
                    };
                    nodes.push(node);
                    scene.add(node);
                }
            });
        }

        // Vertical traces
        if (Math.random() > 0.2) {
            const points = [];
            const x = i + (Math.random() - 0.5) * 0.3;
            const startY = -gridSize / 2;
            const endY = gridSize / 2;

            let currentY = startY;
            points.push(new THREE.Vector3(x, currentY, 0));

            while (currentY < endY) {
                currentY += Math.random() * 2 + 0.5;
                if (Math.random() > 0.75) {
                    const horizontalOffset = (Math.random() - 0.5) * 1.5;
                    points.push(new THREE.Vector3(x + horizontalOffset, currentY, 0));
                } else {
                    points.push(new THREE.Vector3(x, currentY, 0));
                }
            }

            const geometry = new THREE.BufferGeometry().setFromPoints(points);
            const neonColor = Math.random() > 0.5 ? colors.secondary : colors.primary;
            const material = new THREE.LineBasicMaterial({
                color: colors.inactive,
                transparent: true,
                opacity: 0.3,
                linewidth: 1,
            });

            const line = new THREE.Line(geometry, material);
            line.userData = {
                baseOpacity: 0.3,
                neonColor: neonColor,
                inactiveColor: colors.inactive,
                glowIntensity: 0,
                originalPositions: new Float32Array(geometry.attributes.position.array)
            };
            circuitLines.push(line);
            scene.add(line);

            // Add connection nodes
            points.forEach((point, idx) => {
                if (idx % 4 === 0) {
                    const nodeGeometry = new THREE.SphereGeometry(0.08, 6, 6);
                    const nodeMaterial = new THREE.MeshBasicMaterial({
                        color: colors.inactive,
                        transparent: true,
                        opacity: 0.4,
                    });
                    const node = new THREE.Mesh(nodeGeometry, nodeMaterial);
                    node.position.copy(point);
                    node.userData = {
                        baseOpacity: 0.4,
                        glowIntensity: 0,
                        neonColor: neonColor,
                        inactiveColor: colors.inactive,
                        originalPosition: point.clone()
                    };
                    nodes.push(node);
                    scene.add(node);
                }
            });
        }
    }

    camera.position.z = 25;  // Pull back for full view

    // Mouse interaction
    let mouseX = 0;
    let mouseY = 0;
    const mouse3D = new THREE.Vector3();

    document.addEventListener('mousemove', (event) => {
        mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        mouseY = -(event.clientY / window.innerHeight) * 2 + 1;

        // Convert to 3D coordinates
        mouse3D.set(
            mouseX * 25,
            mouseY * 25,
            0
        );
    });

    // Animation
    const clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);

        const elapsedTime = clock.getElapsedTime();
        const moveThreshold = 12;  // Distance for movement effect
        const colorThreshold = 10; // Distance for color change

        // Update circuit lines
        circuitLines.forEach((line) => {
            const position = line.geometry.attributes.position;
            const originalPositions = line.userData.originalPositions;
            let closestDistance = Infinity;
            let avgGlowIntensity = 0;

            // Calculate displacement and color for each vertex
            for (let i = 0; i < position.count; i++) {
                const originalVertex = new THREE.Vector3(
                    originalPositions[i * 3],
                    originalPositions[i * 3 + 1],
                    originalPositions[i * 3 + 2]
                );

                const distance = originalVertex.distanceTo(mouse3D);
                closestDistance = Math.min(closestDistance, distance);

                // Localized movement - only vertices near mouse move
                if (distance < moveThreshold) {
                    const moveIntensity = 1 - (distance / moveThreshold);
                    const direction = new THREE.Vector3()
                        .subVectors(originalVertex, mouse3D)
                        .normalize();

                    // Soft wave displacement
                    const displacement = Math.sin(elapsedTime * 2 + distance * 0.5) * moveIntensity * 0.3;
                    const offset = direction.multiplyScalar(displacement);

                    position.setXYZ(
                        i,
                        originalVertex.x + offset.x,
                        originalVertex.y + offset.y,
                        originalVertex.z + offset.z
                    );
                } else {
                    // Reset to original position
                    position.setXYZ(i, originalVertex.x, originalVertex.y, originalVertex.z);
                }

                // Track glow intensity
                if (distance < colorThreshold) {
                    avgGlowIntensity += 1 - (distance / colorThreshold);
                }
            }

            position.needsUpdate = true;
            avgGlowIntensity /= position.count;

            // Smooth color transition from gray to neon
            if (closestDistance < colorThreshold) {
                const colorIntensity = 1 - (closestDistance / colorThreshold);
                line.userData.glowIntensity = THREE.MathUtils.lerp(
                    line.userData.glowIntensity,
                    colorIntensity,
                    0.1  // Smooth transition
                );
            } else {
                line.userData.glowIntensity = THREE.MathUtils.lerp(
                    line.userData.glowIntensity,
                    0,
                    0.05  // Slower fade back to gray
                );
            }

            // Interpolate between inactive gray and neon color
            const inactiveColor = new THREE.Color(line.userData.inactiveColor);
            const activeColor = new THREE.Color(line.userData.neonColor);
            const currentColor = inactiveColor.lerp(activeColor, line.userData.glowIntensity);

            line.material.color.copy(currentColor);
            line.material.opacity = line.userData.baseOpacity + line.userData.glowIntensity * 0.4;
        });

        // Update nodes
        nodes.forEach((node) => {
            const originalPos = node.userData.originalPosition;
            const distance = originalPos.distanceTo(mouse3D);

            // Localized movement for nodes
            if (distance < moveThreshold) {
                const moveIntensity = 1 - (distance / moveThreshold);
                const direction = new THREE.Vector3()
                    .subVectors(originalPos, mouse3D)
                    .normalize();

                const displacement = Math.sin(elapsedTime * 3 + distance) * moveIntensity * 0.2;
                const offset = direction.multiplyScalar(displacement);

                node.position.copy(originalPos).add(offset);
            } else {
                node.position.copy(originalPos);
            }

            // Color transition
            if (distance < colorThreshold) {
                const colorIntensity = 1 - (distance / colorThreshold);
                node.userData.glowIntensity = THREE.MathUtils.lerp(
                    node.userData.glowIntensity,
                    colorIntensity,
                    0.1
                );

                // Scale effect
                node.scale.setScalar(1 + node.userData.glowIntensity * 0.3);

                // Pulse
                const pulse = Math.sin(elapsedTime * 8) * 0.05 + 1;
                node.scale.multiplyScalar(pulse);
            } else {
                node.userData.glowIntensity = THREE.MathUtils.lerp(
                    node.userData.glowIntensity,
                    0,
                    0.05
                );
                node.scale.setScalar(1);
            }

            // Color interpolation
            const inactiveColor = new THREE.Color(node.userData.inactiveColor);
            const activeColor = new THREE.Color(node.userData.neonColor);
            const currentColor = inactiveColor.lerp(activeColor, node.userData.glowIntensity);

            node.material.color.copy(currentColor);
            node.material.opacity = node.userData.baseOpacity + node.userData.glowIntensity * 0.3;
        });

        // Very subtle global rotation
        scene.rotation.z = Math.sin(elapsedTime * 0.05) * 0.01;

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
        const newColors = getThemeColors();

        circuitLines.forEach(line => {
            line.userData.inactiveColor = newColors.inactive;
            // Update neon color based on previous assignment
            const wasSecondary = line.userData.neonColor === colors.secondary;
            const wasPrimary = line.userData.neonColor === colors.primary;

            if (wasSecondary) {
                line.userData.neonColor = newColors.secondary;
            } else if (wasPrimary) {
                line.userData.neonColor = newColors.primary;
            } else {
                line.userData.neonColor = newColors.accent;
            }
        });

        nodes.forEach(node => {
            node.userData.inactiveColor = newColors.inactive;
            const wasSecondary = node.userData.neonColor === colors.secondary;
            const wasPrimary = node.userData.neonColor === colors.primary;

            if (wasSecondary) {
                node.userData.neonColor = newColors.secondary;
            } else if (wasPrimary) {
                node.userData.neonColor = newColors.primary;
            } else {
                node.userData.neonColor = newColors.accent;
            }
        });

        Object.assign(colors, newColors);
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-theme']
    });

    return { scene, camera, renderer, circuitLines, nodes };
}
