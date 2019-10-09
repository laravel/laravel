require('tap').mochaGlobals()
const assert = require('assert')
const remark = require('remark')
const frontmatter = require('remark-frontmatter')
const extract = require('remark-extract-frontmatter')
const yaml = require('yaml').parse
const strip = require('strip-markdown')
const vfile = require('to-vfile')
const syllable = require('syllable')
const fs = require('fs')

const dir = './_haikus/'
const blockList = ['haikus.md']

const files = fs.readdirSync(dir)

files.forEach(async (file) => {
  if (!blockList.includes(file)) {
    const [text, meta] = await processMarkdown(dir + file)
    const lines = text.split('\n').filter((line) => line !== '')

    if (meta.test !== false) {
      validateHaiku(file, lines, meta)
    }
  }
})

function processMarkdown(filename) {
  return new Promise((resolve, reject) => {
    remark()
      .use(frontmatter)
      .use(extract, { yaml: yaml })
      .use(strip)
      .process(vfile.readSync(filename), (err, file) => {
        if (err) {
          reject(err)
        } else {
          resolve([file.toString(), file.data])
        }
      })
  })
}

function validateHaiku(filename, lines, meta) {
  describe(filename, () => {
    it("should have a '.md' file extension", () => {
      assert.ok(/\.md$/.test(filename), "extension does not match '.md'")
    })

    describe('file metadata', () => {
      it("should have layout equal to 'haiku'", () => {
        assert.equal(
          meta.layout,
          'haiku',
          "layout metadata should equal 'haiku'"
        )
      })

      it('should have non-blank title', () => {
        assert.equal(typeof meta.title, 'string', 'title metadata is missing')
      })

      it('should have non-blank author', () => {
        assert.equal(typeof meta.author, 'string', 'author metadata is missing')
      })
    })

    describe('haiku structure', () => {
      it('should have three lines', () => {
        assert.equal(lines.length, 3)
      })

      it('should have five syllables on the first line', () => {
        assert.equal(syllable(lines[0]), 5)
      })

      it('should have seven syllables on the second line', () => {
        assert.equal(syllable(lines[1]), 7)
      })

      it('should have five syllables on the third line', () => {
        assert.equal(syllable(lines[2]), 5)
      })
    })
  })
}
