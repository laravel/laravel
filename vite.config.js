import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

// Helper function to copy manifest file with enhanced path detection
const copyManifestFile = () => {
    // Check multiple possible locations where Vite might place the manifest
    const possibleManifestPaths = [
        path.resolve(__dirname, 'public/build/.vite/manifest.json'),
        path.resolve(__dirname, 'public/.vite/manifest.json'),
        path.resolve(__dirname, 'public/build/manifest.json'),
        path.resolve(__dirname, '.vite/manifest.json')
    ];
    
    let viteManifestPath = null;
    
    // Find the first existing manifest file
    for (const manifestPath of possibleManifestPaths) {
        if (fs.existsSync(manifestPath)) {
            viteManifestPath = manifestPath;
            console.log(`✅ Found manifest at: ${manifestPath}`);
            break;
        }
    }
    
    if (!viteManifestPath) {
        console.log('❌ Vite manifest not found in any of the expected locations:');
        possibleManifestPaths.forEach(path => console.log(` - ${path}`));
        return false;
    }
    
    // Create the target directory for the manifest
    const targetManifestPath = path.resolve(__dirname, 'public/build/manifest.json');
    const targetDir = path.dirname(targetManifestPath);
    
    try {
        // Create directories if needed
        if (!fs.existsSync(targetDir)) {
            fs.mkdirSync(targetDir, { recursive: true });
            console.log(`Created directory: ${targetDir}`);
        }
        
        // Copy the manifest file
        fs.copyFileSync(viteManifestPath, targetManifestPath);
        console.log('✅ Manifest file copied to public/build/manifest.json');
        return true;
    } catch (error) {
        console.error('❌ Error copying manifest file:', error.message);
        return false;
    }
};

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Enhanced plugin to handle both development and build modes
        {
            name: 'copy-manifest',
            // For production builds
            closeBundle: async () => {
                // Wait a moment for files to be written
                await new Promise(resolve => setTimeout(resolve, 500));
                copyManifestFile();
            },
            // For development mode
            configureServer(server) {
                // Initial check
                setTimeout(() => {
                    copyManifestFile();
                }, 1000);
                
                // Setup periodic checks for the manifest file during development
                const interval = setInterval(() => {
                    copyManifestFile();
                }, 2000);
                
                // Clean up on close
                server.httpServer.on('close', () => {
                    clearInterval(interval);
                });
            }
        },
    ],
    build: {
        // Ensure manifest.json is placed in public/build/ instead of public/build/.vite/
        manifest: true,
        outDir: 'public/build',
    },
});
