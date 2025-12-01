// 3D Neon Circuit Board Background - Dark Theme Only
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

    // Neon colors for dark theme
    const colors = {
        primary: 0x00ccff,    // Light Blue
        secondary: 0xff0099,  // Pink
        accent: 0x00ff88,     // Teal
        inactive: 0x666666,   // Dark Gray
    };

    const circuitLines = [];
    const nodes = [];

    // Create circuit board pattern
    const gridSize = 50;
    const spacing = 1.5;

    // Generate horizontal and vertical traces
    for (let i = -gridSize / 2; i < gridSize / 2; i += spacing) {
        // Horizontal traces
        if (Math.random() > 0.2) {
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

    camera.position.z = 25;

    // Mouse interaction
    let mouseX = 0;
    let mouseY = 0;
    const mouse3D = new THREE.Vector3();

    document.addEventListener('mousemove', (event) => {
        mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        mouseY = -(event.clientY / window.innerHeight) * 2 + 1;

        mouse3D.set(mouseX * 25, mouseY * 25, 0);
    });

    // Animation
    const clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);

        const elapsedTime = clock.getElapsedTime();
        const moveThreshold = 12;
        const colorThreshold = 10;

        circuitLines.forEach((line) => {
            const position = line.geometry.attributes.position;
            const originalPositions = line.userData.originalPositions;
            let closestDistance = Infinity;

            for (let i = 0; i < position.count; i++) {
                const originalVertex = new THREE.Vector3(
                    originalPositions[i * 3],
                    originalPositions[i * 3 + 1],
                    originalPositions[i * 3 + 2]
                );

                const distance = originalVertex.distanceTo(mouse3D);
                closestDistance = Math.min(closestDistance, distance);

                if (distance < moveThreshold) {
                    const moveIntensity = 1 - (distance / moveThreshold);
                    const direction = new THREE.Vector3()
                        .subVectors(originalVertex, mouse3D)
                        .normalize();

                    const displacement = Math.sin(elapsedTime * 2 + distance * 0.5) * moveIntensity * 0.3;
                    const offset = direction.multiplyScalar(displacement);

                    position.setXYZ(
                        i,
                        originalVertex.x + offset.x,
                        originalVertex.y + offset.y,
                        originalVertex.z + offset.z
                    );
                } else {
                    position.setXYZ(i, originalVertex.x, originalVertex.y, originalVertex.z);
                }
            }

            position.needsUpdate = true;

            if (closestDistance < colorThreshold) {
                const colorIntensity = 1 - (closestDistance / colorThreshold);
                line.userData.glowIntensity = THREE.MathUtils.lerp(
                    line.userData.glowIntensity,
                    colorIntensity,
                    0.1
                );
            } else {
                line.userData.glowIntensity = THREE.MathUtils.lerp(
                    line.userData.glowIntensity,
                    0,
                    0.05
                );
            }

            const inactiveColor = new THREE.Color(line.userData.inactiveColor);
            const activeColor = new THREE.Color(line.userData.neonColor);
            const currentColor = inactiveColor.lerp(activeColor, line.userData.glowIntensity);

            line.material.color.copy(currentColor);
            line.material.opacity = line.userData.baseOpacity + line.userData.glowIntensity * 0.4;
        });

        nodes.forEach((node) => {
            const originalPos = node.userData.originalPosition;
            const distance = originalPos.distanceTo(mouse3D);

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

            if (distance < colorThreshold) {
                const colorIntensity = 1 - (distance / colorThreshold);
                node.userData.glowIntensity = THREE.MathUtils.lerp(
                    node.userData.glowIntensity,
                    colorIntensity,
                    0.1
                );

                node.scale.setScalar(1 + node.userData.glowIntensity * 0.3);

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

            const inactiveColor = new THREE.Color(node.userData.inactiveColor);
            const activeColor = new THREE.Color(node.userData.neonColor);
            const currentColor = inactiveColor.lerp(activeColor, node.userData.glowIntensity);

            node.material.color.copy(currentColor);
            node.material.opacity = node.userData.baseOpacity + node.userData.glowIntensity * 0.3;
        });

        scene.rotation.z = Math.sin(elapsedTime * 0.05) * 0.01;

        renderer.render(scene, camera);
    }

    animate();

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    });

    return { scene, camera, renderer, circuitLines, nodes };
}
