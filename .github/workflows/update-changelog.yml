name: "Update Changelog"

on:
  release:
    types: [released]

jobs:
  update:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v2
        with:
          ref: ${{ github.ref_name }}

      - name: Update Changelog
        uses: stefanzweifel/changelog-updater-action@v1
        with:
          latest-version: ${{ github.event.release.tag_name }}
          release-notes: ${{ github.event.release.body }}
          compare-url-target-revision: ${{ github.event.release.target_commitish }}

      - name: Commit updated CHANGELOG
        uses: stefanzweifel/git-auto-commit-action@v4
        with:
          branch: ${{ github.event.release.target_commitish }}
          commit_message: Update CHANGELOG.md
          file_pattern: CHANGELOG.md
