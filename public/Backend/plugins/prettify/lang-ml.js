// Copyright (C) 2008 Google Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.



/**
 * @fileoverview
 * Registers a language handler for OCaml, SML, F# and similar languages.
 *
 * Based on the lexical grammar at
 * http://research.microsoft.com/en-us/um/cambridge/projects/fsharp/manual/spec.html#_Toc270597388
 *
 * @author mikesamuel@gmail.com
 */

PR['registerLangHandler'](
    PR['createSimpleLexer'](
        [
         // Whitespace is made up of spaces, tabs and newline characters.
         [PR['PR_PLAIN'],       /^[\t\n\r \xA0]+/, null, '\t\n\r \xA0'],
         // #if ident/#else/#endif directives delimit conditional compilation
         // sections
         [PR['PR_COMMENT'],
          /^#(?:if[\t\n\r \xA0]+(?:[a-z_$][\w\']*|``[^\r\n\t`]*(?:``|$))|else|endif|light)/i,
          null, '#'],
         // A double or single quoted, possibly multi-line, string.
         // F# allows escaped newlines in strings.
         [PR['PR_STRING'],      /^(?:\"(?:[^\"\\]|\\[\s\S])*(?:\"|$)|\'(?:[^\'\\]|\\[\s\S])(?:\'|$))/, null, '"\'']
        ],
        [
         // Block comments are delimited by (* and *) and may be
         // nested. Single-line comments begin with // and extend to
         // the end of a line.
         // TODO: (*...*) comments can be nested.  This does not handle that.
         [PR['PR_COMMENT'],     /^(?:\/\/[^\r\n]*|\(\*[\s\S]*?\*\))/],
         [PR['PR_KEYWORD'],     /^(?:abstract|and|as|assert|begin|class|default|delegate|do|done|downcast|downto|elif|else|end|exception|extern|false|finally|for|fun|function|if|in|inherit|inline|interface|internal|lazy|let|match|member|module|mutable|namespace|new|null|of|open|or|override|private|public|rec|return|static|struct|then|to|true|try|type|upcast|use|val|void|when|while|with|yield|asr|land|lor|lsl|lsr|lxor|mod|sig|atomic|break|checked|component|const|constraint|constructor|continue|eager|event|external|fixed|functor|global|include|method|mixin|object|parallel|process|protected|pure|sealed|trait|virtual|volatile)\b/],
         // A number is a hex integer literal, a decimal real literal, or in
         // scientific notation.
         [PR['PR_LITERAL'],
          /^[+\-]?(?:0x[\da-f]+|(?:(?:\.\d+|\d+(?:\.\d*)?)(?:e[+\-]?\d+)?))/i],
         [PR['PR_PLAIN'],       /^(?:[a-z_][\w']*[!?#]?|``[^\r\n\t`]*(?:``|$))/i],
         // A printable non-space non-special character
         [PR['PR_PUNCTUATION'], /^[^\t\n\r \xA0\"\'\w]+/]
        ]),
    ['fs', 'ml']);
