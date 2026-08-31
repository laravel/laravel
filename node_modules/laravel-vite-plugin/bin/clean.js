#!/usr/bin/env node

import { readFileSync, readdirSync, unlinkSync, existsSync } from 'fs'
import { dirname } from 'path'

/*
 * Argv helpers.
 */

const argument = (name) => {
    const index = process.argv.findIndex(argument => argument.startsWith(`--${name}=`))

    return index === -1
        ? undefined
        : process.argv[index].substring(`--${name}=`.length)
}

const option = (name) => process.argv.includes(`--${name}`)

/*
 * Helpers.
 */
const info = option(`quiet`) ? (() => undefined) : console.log
const error = option(`quiet`) ? (() => undefined) : console.error

/*
 * Clean.
 */

const main = () => {
    const manifestPaths = argument(`manifest`) ? [argument(`manifest`)] : (option(`ssr`)
        ? [`./bootstrap/ssr/ssr-manifest.json`, `./bootstrap/ssr/manifest.json`]
        : [`./public/build/manifest.json`])

    const foundManifestPath = manifestPaths.find(existsSync)

    if (! foundManifestPath) {
        error(`Unable to find manifest file.`)

        process.exit(1)
    }

    info(`Reading manifest [${foundManifestPath}].`)

    const manifest = JSON.parse(readFileSync(foundManifestPath).toString())

    const manifestFiles = Object.keys(manifest)

    const isSsr = Array.isArray(manifest[manifestFiles[0]])

    isSsr
        ? info(`SSR manifest found.`)
        : info(`Non-SSR manifest found.`)

    const manifestAssets = isSsr
        ? manifestFiles.flatMap(key => manifest[key])
        : manifestFiles.flatMap(key => [
            ...manifest[key].css ?? [],
            manifest[key].file,
        ])

    const assetsPath = argument('assets') ?? dirname(foundManifestPath)+'/assets'

    info(`Verify assets in [${assetsPath}].`)

    const existingAssets = readdirSync(assetsPath, { withFileTypes: true })

    const orphanedAssets = existingAssets.filter(file => file.isFile())
        .filter(file => manifestAssets.findIndex(asset => asset.endsWith(`/${file.name}`)) === -1)

    if (orphanedAssets.length === 0) {
        info(`No ophaned assets found.`)
    } else {
        orphanedAssets.length === 1
            ? info(`[${orphanedAssets.length}] orphaned asset found.`)
            : info(`[${orphanedAssets.length}] orphaned assets found.`)

        orphanedAssets.forEach(asset => {
            const path = `${assetsPath}/${asset.name}`

            option(`dry-run`)
                ? info(`Orphaned asset [${path}] would be removed.`)
                : info(`Removing orphaned asset [${path}].`)

            if (! option(`dry-run`)) {
                unlinkSync(path)
            }
        })
    }
}

main()
