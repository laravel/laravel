/**
 * Tagify
 */

'use strict';

(function () {
  // Basic
  //------------------------------------------------------
  const tagifyBasicEl = document.querySelector('#TagifyBasic');
  const TagifyBasic = new Tagify(tagifyBasicEl);

  // Read only
  //------------------------------------------------------
  const tagifyReadonlyEl = document.querySelector('#TagifyReadonly');
  const TagifyReadonly = new Tagify(tagifyReadonlyEl);

  // Custom list & inline suggestion
  //------------------------------------------------------
  const TagifyCustomInlineSuggestionEl = document.querySelector('#TagifyCustomInlineSuggestion');
  const TagifyCustomListSuggestionEl = document.querySelector('#TagifyCustomListSuggestion');

  const whitelist = [
    'A# .NET',
    'A# (Axiom)',
    'A-0 System',
    'A+',
    'A++',
    'ABAP',
    'ABC',
    'ABC ALGOL',
    'ABSET',
    'ABSYS',
    'ACC',
    'Accent',
    'Ace DASL',
    'ACL2',
    'Avicsoft',
    'ACT-III',
    'Action!',
    'ActionScript',
    'Ada',
    'Adenine',
    'Agda',
    'Agilent VEE',
    'Agora',
    'AIMMS',
    'Alef',
    'ALF',
    'ALGOL 58',
    'ALGOL 60',
    'ALGOL 68',
    'ALGOL W',
    'Alice',
    'Alma-0',
    'AmbientTalk',
    'Amiga E',
    'AMOS',
    'AMPL',
    'Apex (Salesforce.com)',
    'APL',
    'AppleScript',
    'Arc',
    'ARexx',
    'Argus',
    'AspectJ',
    'Assembly language',
    'ATS',
    'Ateji PX',
    'AutoHotkey',
    'Autocoder',
    'AutoIt',
    'AutoLISP / Visual LISP',
    'Averest',
    'AWK',
    'Axum',
    'Active Server Pages',
    'ASP.NET'
  ];
  // Inline
  let TagifyCustomInlineSuggestion = new Tagify(TagifyCustomInlineSuggestionEl, {
    whitelist: whitelist,
    maxTags: 10,
    dropdown: {
      maxItems: 20,
      classname: 'tags-inline',
      enabled: 0,
      closeOnSelect: false
    }
  });
  // List
  let TagifyCustomListSuggestion = new Tagify(TagifyCustomListSuggestionEl, {
    whitelist: whitelist,
    maxTags: 10,
    dropdown: {
      maxItems: 20,
      classname: '',
      enabled: 0,
      closeOnSelect: false
    }
  });

  // Users List suggestion
  //------------------------------------------------------
  const TagifyUserListEl = document.querySelector('#TagifyUserList');

  const usersList = [
    {
      value: 1,
      name: 'Justinian Hattersley',
      avatar: 'https://i.pravatar.cc/80?img=1',
      email: 'jhattersley0@ucsd.edu'
    },
    {
      value: 2,
      name: 'Antons Esson',
      avatar: 'https://i.pravatar.cc/80?img=2',
      email: 'aesson1@ning.com'
    },
    {
      value: 3,
      name: 'Ardeen Batisse',
      avatar: 'https://i.pravatar.cc/80?img=3',
      email: 'abatisse2@nih.gov'
    },
    {
      value: 4,
      name: 'Graeme Yellowley',
      avatar: 'https://i.pravatar.cc/80?img=4',
      email: 'gyellowley3@behance.net'
    },
    {
      value: 5,
      name: 'Dido Wilford',
      avatar: 'https://i.pravatar.cc/80?img=5',
      email: 'dwilford4@jugem.jp'
    },
    {
      value: 6,
      name: 'Celesta Orwin',
      avatar: 'https://i.pravatar.cc/80?img=6',
      email: 'corwin5@meetup.com'
    },
    {
      value: 7,
      name: 'Sally Main',
      avatar: 'https://i.pravatar.cc/80?img=7',
      email: 'smain6@techcrunch.com'
    },
    {
      value: 8,
      name: 'Grethel Haysman',
      avatar: 'https://i.pravatar.cc/80?img=8',
      email: 'ghaysman7@mashable.com'
    },
    {
      value: 9,
      name: 'Marvin Mandrake',
      avatar: 'https://i.pravatar.cc/80?img=9',
      email: 'mmandrake8@sourceforge.net'
    },
    {
      value: 10,
      name: 'Corrie Tidey',
      avatar: 'https://i.pravatar.cc/80?img=10',
      email: 'ctidey9@youtube.com'
    }
  ];

  function tagTemplate(tagData) {
    return `
    <tag title="${tagData.title || tagData.email}"
      contenteditable='false'
      spellcheck='false'
      tabIndex="-1"
      class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
      ${this.getAttributes(tagData)}
    >
      <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
      <div>
        <div class='tagify__tag__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>
        <span class='tagify__tag-text'>${tagData.name}</span>
      </div>
    </tag>
  `;
  }

  function suggestionItemTemplate(tagData) {
    return `
    <div ${this.getAttributes(tagData)}
      class='tagify__dropdown__item align-items-center ${tagData.class ? tagData.class : ''}'
      tabindex="0"
      role="option"
    >
      ${
        tagData.avatar
          ? `<div class='tagify__dropdown__item__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>`
          : ''
      }
      <strong>${tagData.name}</strong>
      <span>${tagData.email}</span>
    </div>
  `;
  }

  // initialize Tagify on the above input node reference
  let TagifyUserList = new Tagify(TagifyUserListEl, {
    tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
    enforceWhitelist: true,
    skipInvalid: true, // do not remporarily add invalid tags
    dropdown: {
      closeOnSelect: false,
      enabled: 0,
      classname: 'users-list',
      searchKeys: ['name', 'email'] // very important to set by which keys to search for suggesttions when typing
    },
    templates: {
      tag: tagTemplate,
      dropdownItem: suggestionItemTemplate
    },
    whitelist: usersList
  });

  TagifyUserList.on('dropdown:show dropdown:updated', onDropdownShow);
  TagifyUserList.on('dropdown:select', onSelectSuggestion);

  let addAllSuggestionsEl;

  function onDropdownShow(e) {
    let dropdownContentEl = e.detail.tagify.DOM.dropdown.content;

    if (TagifyUserList.suggestedListItems.length > 1) {
      addAllSuggestionsEl = getAddAllSuggestionsEl();

      // insert "addAllSuggestionsEl" as the first element in the suggestions list
      dropdownContentEl.insertBefore(addAllSuggestionsEl, dropdownContentEl.firstChild);
    }
  }

  function onSelectSuggestion(e) {
    if (e.detail.elm == addAllSuggestionsEl) TagifyUserList.dropdown.selectAll.call(TagifyUserList);
  }

  // create an "add all" custom suggestion element every time the dropdown changes
  function getAddAllSuggestionsEl() {
    // suggestions items should be based on "dropdownItem" template
    return TagifyUserList.parseTemplate('dropdownItem', [
      {
        class: 'addAll',
        name: 'Add all',
        email:
          TagifyUserList.settings.whitelist.reduce(function (remainingSuggestions, item) {
            return TagifyUserList.isTagDuplicate(item.value) ? remainingSuggestions : remainingSuggestions + 1;
          }, 0) + ' Members'
      }
    ]);
  }

  // Email List suggestion
  //------------------------------------------------------
  // generate random whitelist items (for the demo)
  let randomStringsArr = Array.apply(null, Array(100)).map(function () {
    return (
      Array.apply(null, Array(~~(Math.random() * 10 + 3)))
        .map(function () {
          return String.fromCharCode(Math.random() * (123 - 97) + 97);
        })
        .join('') + '@gmail.com'
    );
  });

  const TagifyEmailListEl = document.querySelector('#TagifyEmailList'),
    TagifyEmailList = new Tagify(TagifyEmailListEl, {
      // email address validation (https://stackoverflow.com/a/46181/104380)
      pattern:
        /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
      whitelist: randomStringsArr,
      callbacks: {
        invalid: onInvalidTag
      },
      dropdown: {
        position: 'text',
        enabled: 1 // show suggestions dropdown after 1 typed character
      }
    }),
    button = TagifyEmailListEl.nextElementSibling; // "add new tag" action-button

  button.addEventListener('click', onAddButtonClick);

  function onAddButtonClick() {
    TagifyEmailList.addEmptyTag();
  }

  function onInvalidTag(e) {
    console.log('invalid', e.detail);
  }
})();
