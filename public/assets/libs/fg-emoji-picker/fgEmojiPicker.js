/*

// Usage
FgEmojiPicker.init({
    trigger: 'button',
    position: ['bottom', 'right'],
    dir: 'directory/to/json', (without json name)
    preFetch: true, //load emoji json when function called 
    emit(emoji) {
        console.log(emoji);
    }
});
*/

const FgEmojiPicker = function (options) {

    this.options = options;
    this.emojiJson = null;

    if (!this.options) {
        return console.error('You must provide object as a first argument')
    }

    this.init = () => {

        // Generate style
        this.functions.generateStyle.call(this);

        this.selectors.trigger = this.options.hasOwnProperty('trigger') ? this.options.trigger : console.error('You must proved trigger element like this - \'EmojiPicker.init({trigger: "selector"})\' ');
        this.selectors.search = '.fg-emoji-picker-search input';
        this.selectors.emojiContainer = '.fg-emoji-picker-grid'
        this.emojiItems = undefined;
        this.variable.emit = this.options.emit || null;
        this.variable.removeOnSelection = this.options.removeOnSelection || false;
        this.variable.closeButton = this.options.closeButton || true;
        this.variable.position = this.options.position || null;
        this.variable.dir = this.options.dir || '';
        this.insertInto = this.options.insertInto || undefined;
        if (!this.selectors.trigger) return;

        this.bindEvents();
        this.options && this.options.preFetch && this.functions.fetchEmojiData();
    }

    this.lib = (el = undefined) => {
        return {
            el: document.querySelectorAll(el),
            on(event, callback, classList = undefined) {
                if (!classList) {
                    this.el.forEach(item => {
                        item.addEventListener(event, callback.bind(item))
                    })
                } else {
                    this.el.forEach(item => {
                        item.addEventListener(event, (e) => {
                            if (e.target.closest(classList)) {
                                callback.call(e.target.closest(classList), e)
                            }
                        })
                    })
                }
            }
        }
    },

    this.variable = {
        position: null,
        dir: '',
        categoryIcons: {
            'smileys--people': '<svg width="20" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <g> <path d="M437.02,74.98C388.667,26.629,324.38,0,256,0S123.333,26.629,74.98,74.98C26.629,123.333,0,187.62,0,256 s26.629,132.668,74.98,181.02C123.333,485.371,187.62,512,256,512s132.667-26.629,181.02-74.98 C485.371,388.668,512,324.38,512,256S485.371,123.333,437.02,74.98z M256,472c-119.103,0-216-96.897-216-216S136.897,40,256,40 s216,96.897,216,216S375.103,472,256,472z"/> </g> </g> <g> <g> <path d="M368.993,285.776c-0.072,0.214-7.298,21.626-25.02,42.393C321.419,354.599,292.628,368,258.4,368 c-34.475,0-64.195-13.561-88.333-40.303c-18.92-20.962-27.272-42.54-27.33-42.691l-37.475,13.99 c0.42,1.122,10.533,27.792,34.013,54.273C171.022,389.074,212.215,408,258.4,408c46.412,0,86.904-19.076,117.099-55.166 c22.318-26.675,31.165-53.55,31.531-54.681L368.993,285.776z"/> </g> </g> <g> <g> <circle cx="168" cy="180.12" r="32"/> </g> </g> <g> <g> <circle cx="344" cy="180.12" r="32"/> </g> </g> <g> </g> <g> </g> <g> </g> </svg>',
            'animals--nature': '<svg width="20" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 354.968 354.968" style="enable-background:new 0 0 354.968 354.968;" xml:space="preserve"> <g> <g> <path d="M350.775,341.319c-9.6-28.4-20.8-55.2-34.4-80.8c0.4-0.4,0.8-1.2,1.6-1.6c30.8-34.8,44-83.6,20.4-131.6 c-20.4-41.6-65.6-76.4-124.8-98.8c-57.2-22-127.6-32.4-200.4-27.2c-5.6,0.4-10,5.2-9.6,10.8c0.4,2.8,1.6,5.6,4,7.2 c36.8,31.6,50,79.2,63.6,126.8c8,28,15.6,55.6,28.4,81.2c0,0.4,0.4,0.4,0.4,0.8c30.8,59.6,78,81.2,122.8,78.4 c18.4-1.2,36-6.4,52.4-14.4c9.2-4.8,18-10.4,26-16.8c11.6,23.2,22,47.2,30.4,72.8c1.6,5.2,7.6,8,12.8,6.4 C349.975,352.119,352.775,346.519,350.775,341.319z M271.175,189.319c-34.8-44.4-78-82.4-131.6-112.4c-4.8-2.8-11.2-1.2-13.6,4 c-2.8,4.8-1.2,11.2,4,13.6c50.8,28.8,92.4,64.8,125.6,107.2c13.2,17.2,25.2,35.2,36,54c-8,7.6-16.4,13.6-25.6,18 c-14,7.2-28.8,11.6-44.4,12.4c-37.6,2.4-77.2-16-104-67.6v-0.4c-11.6-24-19.2-50.8-26.8-78c-12.4-43.2-24.4-86.4-53.6-120.4 c61.6-1.6,120.4,8.4,169.2,27.2c54.4,20.8,96,52,114,88.8c18.8,38,9.2,76.8-14.4,105.2 C295.575,222.919,283.975,205.719,271.175,189.319z"/> </g> </g> <g> </g> <g> </g> <g> </g> </svg>',
            'travel--places': '<svg width="20" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.003 512.003" style="enable-background:new 0 0 512.003 512.003;" xml:space="preserve"> <g> <g> <path d="M492.638,416.168c-35.445-29.635-139.695-117.048-187.5-159.684c25.879-23.721,62.112-58.999,97.354-93.327 c40.344-39.302,78.458-76.417,95.729-91c8.76-7.385,13.781-18.24,13.781-29.781c0-14.438-7.51-27.24-20.094-34.24 c-12.521-6.958-27.26-6.615-39.438,0.948c-39.313,24.385-196.469,137.25-196.469,161.583c0,7.178-0.293,15.621-2.85,25.203 c-11.751-20.152-18.483-38.615-18.483-46.536c0-15.177-51.573-64.615-153.292-146.958c-4.219-3.427-10.385-3.115-14.25,0.75 l-64,64c-3.865,3.854-4.188,10.01-0.75,14.25c82.344,101.719,131.781,153.292,146.958,153.292c9.056,0,31.823,8.76,55.227,23.852 C157.266,300.56,60.87,381.466,19.388,416.147C7.066,426.449,0.003,441.605,0.003,457.73c0,29.927,24.344,54.271,54.271,54.271 c16.115,0,31.281-7.073,41.583-19.406c35.645-42.613,119.006-141.934,160.151-187.994 c42.456,47.553,130.417,152.456,160.172,188.035c10.292,12.313,25.448,19.365,41.552,19.365c29.927,0,54.271-24.344,54.271-54.271 C512.003,441.626,504.951,426.47,492.638,416.168z M277.336,172.543c11.219-17.521,121.354-104.99,186.385-145.333 c5.5-3.385,12.167-3.573,17.813-0.438c5.719,3.188,9.135,9.021,9.135,15.604c0,5.24-2.26,10.146-6.208,13.479 c-17.854,15.073-56.229,52.448-96.854,92.021c-35.809,34.887-72.602,70.699-98.298,94.167c-1.604-1.518-3.255-3.074-4.431-4.25 c-6.327-6.327-12.008-13.227-17.279-20.24C276.351,199.191,277.309,183.556,277.336,172.543z M79.493,478.907 c-6.24,7.479-15.438,11.76-25.219,11.76c-18.167,0-32.938-14.771-32.938-32.938c0-9.792,4.281-18.979,11.74-25.208 c42.625-35.651,143.104-119.991,189.254-161.26c5.341,4.263,10.582,8.732,15.465,13.615c1.036,1.036,2.443,2.535,3.746,3.905 C200.651,334.428,115.631,435.712,79.493,478.907z M457.732,490.668c-9.781,0-18.958-4.271-25.188-11.719 c-45.26-54.125-153.365-182.854-179.667-209.156c-32.146-32.146-80.802-56.208-101.417-56.208c-0.104,0-0.219,0.01-0.323,0.01 c-12.563-4.885-72.802-72.781-126.125-138.188l6.99-6.99l99.125,99.125c2.083,2.083,4.813,3.125,7.542,3.125 c2.729,0,5.458-1.042,7.542-3.125c4.167-4.167,4.167-10.917,0-15.083L47.086,53.334l6.25-6.25l99.125,99.125 c2.083,2.083,4.813,3.125,7.542,3.125s5.458-1.042,7.542-3.125c4.167-4.167,4.167-10.917,0-15.083L68.42,32.001l6.99-6.99 c65.406,53.323,133.292,113.49,137.927,124.323c0,22.229,24.146,71.229,56.458,103.542 c26.302,26.302,155.031,134.406,209.156,179.656c7.448,6.24,11.719,15.417,11.719,25.198 C490.67,475.897,475.899,490.668,457.732,490.668z"/> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>',
            'activities': '<svg id="Capa_1" enable-background="new 0 0 512 512" height="20" viewBox="0 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg"><path id="XMLID_272_" d="m437.02 74.98c-48.353-48.351-112.64-74.98-181.02-74.98s-132.667 26.629-181.02 74.98c-48.351 48.353-74.98 112.64-74.98 181.02s26.629 132.667 74.98 181.02c48.353 48.351 112.64 74.98 181.02 74.98s132.667-26.629 181.02-74.98c48.351-48.353 74.98-112.64 74.98-181.02s-26.629-132.667-74.98-181.02zm-407.02 181.02c0-57.102 21.297-109.316 56.352-149.142 37.143 45.142 57.438 101.499 57.438 160.409 0 53.21-16.914 105.191-47.908 148.069-40.693-40.891-65.882-97.226-65.882-159.336zm88.491 179.221c35.75-48.412 55.3-107.471 55.3-167.954 0-66.866-23.372-130.794-66.092-181.661 39.718-34.614 91.603-55.606 148.301-55.606 56.585 0 108.376 20.906 148.064 55.396-42.834 50.9-66.269 114.902-66.269 181.872 0 60.556 19.605 119.711 55.448 168.158-38.077 29.193-85.665 46.574-137.243 46.574-51.698 0-99.388-17.461-137.509-46.779zm297.392-19.645c-31.104-42.922-48.088-95.008-48.088-148.309 0-59.026 20.367-115.47 57.638-160.651 35.182 39.857 56.567 92.166 56.567 149.384 0 62.23-25.284 118.665-66.117 159.576z"/></svg>',
            'objects': '<svg width="20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"> <g><g><path d="M846.5,153.5C939,246.1,990,369.1,990,500c0,130.9-51,253.9-143.5,346.5C753.9,939,630.9,990,500,990c-130.9,0-253.9-51-346.5-143.5C61,753.9,10,630.9,10,500c0-130.9,51-253.9,143.5-346.5C246.1,61,369.1,10,500,10C630.9,10,753.9,61,846.5,153.5z M803.2,803.2c60.3-60.3,100.5-135.5,117-217.3c-12.9,19-25.2,26-32.9-16.5c-7.9-69.3-71.5-25-111.5-49.6c-42.1,28.4-136.8-55.2-120.7,39.1c24.8,42.5,134-56.9,79.6,33.1c-34.7,62.8-126.9,201.9-114.9,274c1.5,105-107.3,21.9-144.8-12.9c-25.2-69.8-8.6-191.8-74.6-225.9c-71.6-3.1-133-9.6-160.8-89.6c-16.7-57.3,17.8-142.5,79.1-155.7c89.8-56.4,121.9,66.1,206.1,68.4c26.2-27.4,97.4-36.1,103.4-66.8c-55.3-9.8,70.1-46.5-5.3-67.4c-41.6,4.9-68.4,43.1-46.3,75.6C496,410.3,493.5,274.8,416,317.6c-2,67.6-126.5,21.9-43.1,8.2c28.7-12.5-46.8-48.8-6-42.2c20-1.1,87.4-24.7,69.2-40.6c37.5-23.3,69.1,55.8,105.8-1.8c26.5-44.3-11.1-52.5-44.4-30c-18.7-21,33.1-66.3,78.8-85.9c15.2-6.5,29.8-10.1,40.9-9.1c23,26.6,65.6,31.2,67.8-3.2c-57-27.3-119.9-41.7-185-41.7c-93.4,0-182.3,29.7-255.8,84.6c19.8,9.1,31,20.3,11.9,34.7c-14.8,44.1-74.8,103.2-127.5,94.9c-27.4,47.2-45.4,99.2-53.1,153.6c44.1,14.6,54.3,43.5,44.8,53.2c-22.5,19.6-36.3,47.4-43.4,77.8C91.3,658,132.6,739,196.8,803.2c81,81,188.6,125.6,303.2,125.6C614.5,928.8,722.2,884.2,803.2,803.2z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></g> </svg>',
            'symbols': '<svg width="20" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <g> <path d="M256,0C161.33,0,84.312,77.018,84.312,171.688c0,54.38,25.587,105.127,68.882,137.502c0,7.915,0,121.305,0,125.043 c0,9.22,7.475,16.696,16.696,16.696h10.168C187.72,485.81,218.85,512,256,512c37.15,0,68.28-26.19,75.943-61.072h10.168 c9.22,0,16.696-7.475,16.696-16.696c0-3.762,0-117.209,0-125.043c43.294-32.375,68.882-83.122,68.882-137.502 C427.688,77.018,350.67,0,256,0z M256,478.609c-18.567,0-34.507-11.461-41.116-27.68h82.233 C290.507,467.148,274.567,478.609,256,478.609z M325.415,417.537c-4.855,0-132.083,0-138.83,0v-39.041h138.83V417.537z M211.096,242.095h-8.057c-4.443,0-8.058-3.615-8.058-8.058s3.615-8.057,8.058-8.057c4.443,0,8.057,3.614,8.057,8.057V242.095z M244.488,345.105v-69.619h23.017v69.619H244.488z M332.824,286.684c-4.63,3.099-7.41,8.303-7.41,13.875v44.545h-24.519v-69.619 h8.058c22.855,0,41.449-18.594,41.449-41.45c0-22.855-18.593-41.449-41.449-41.449c-22.855,0-41.45,18.593-41.45,41.449v8.058 h-23.017v-8.058c0-22.855-18.593-41.449-41.449-41.449c-22.855,0-41.45,18.593-41.45,41.449s18.594,41.45,41.45,41.45h8.057 v69.619h-24.511V300.56c0-5.572-2.779-10.776-7.41-13.875c-38.491-25.759-61.472-68.748-61.472-114.996 c0-76.258,62.039-138.297,138.297-138.297S394.297,95.43,394.297,171.688C394.297,217.937,371.316,260.926,332.824,286.684z M300.896,242.095v-8.058c0-4.443,3.615-8.057,8.058-8.057c4.443,0,8.057,3.614,8.057,8.057s-3.614,8.058-8.057,8.058H300.896z"/> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>',
            'flags': '<svg width="20" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="037---Waypoint-Flag" fill="rgb(0,0,0)" fill-rule="nonzero" transform="translate(0 -1)"><path id="Shape" d="m59.0752 28.5054c-3.7664123-1.873859-7.2507049-4.2678838-10.3506-7.1118 1.5923634-6.0211307 2.7737841-12.14349669 3.5361-18.3248.1788-1.44-.623-1.9047-.872-2.0126-.7016942-.26712004-1.4944908-.00419148-1.8975.6293-5.4726 6.5479-12.9687 5.8008-20.9053 5.0054-7.9985-.8-16.2506-1.6116-22.3684 5.4114-.85552122-1.067885-2.26533581-1.5228479-3.5837-1.1565l-.1377.0386c-1.81412367.5095218-2.87378593 2.391025-2.3691 4.2065l12.2089 43.6891c.3541969 1.2645215 1.5052141 2.1399137 2.8184 2.1435.2677318-.0003961.5341685-.0371657.792-.1093l1.0683-.2984h.001c.7485787-.2091577 1.3833789-.7071796 1.7646969-1.3844635.381318-.677284.4779045-1.478326.2685031-2.2268365l-3.7812-13.5327c5.5066-7.0807 13.18-6.3309 21.2988-5.52 8.1094.81 16.4863 1.646 22.64-5.7129l.0029-.0039c.6044387-.7534187.8533533-1.7315007.6826-2.6822-.0899994-.4592259-.3932698-.8481635-.8167-1.0474zm-42.0381 29.7446c-.1201754.2157725-.3219209.3742868-.56.44l-1.0684.2983c-.4949157.1376357-1.0078362-.1513714-1.1465-.646l-12.2095-43.6895c-.20840349-.7523825.23089143-1.5316224.9825-1.7428l.1367-.0381c.12366014-.0348192.25153137-.0524183.38-.0523.63429117.0010181 1.19083557.4229483 1.3631 1.0334l.1083.3876v.0021l6.2529 22.3755 5.8468 20.9238c.0669515.2380103.0360256.4929057-.0859.708zm40.6329-27.2925c-5.4736 6.5459-12.9707 5.7974-20.9043 5.0039-7.9033-.79-16.06-1.605-22.1552 5.1558l-5.463-19.548-2.0643-7.3873c5.5068-7.0794 13.1796-6.3119 21.3045-5.5007 7.7148.7695 15.6787 1.5664 21.7373-4.7095-.7467138 5.70010904-1.859683 11.3462228-3.332 16.9033-.1993066.7185155.0267229 1.4878686.583 1.9844 3.1786296 2.9100325 6.7366511 5.3762694 10.5771 7.3315-.0213812.2768572-.1194065.5422977-.2831.7666z"/></g></g></svg>',
            'search': '<svg width="20" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 511.999 511.999" style="enable-background:new 0 0 511.999 511.999;" xml:space="preserve"> <g> <g> <path d="M508.874,478.708L360.142,329.976c28.21-34.827,45.191-79.103,45.191-127.309c0-111.75-90.917-202.667-202.667-202.667 S0,90.917,0,202.667s90.917,202.667,202.667,202.667c48.206,0,92.482-16.982,127.309-45.191l148.732,148.732 c4.167,4.165,10.919,4.165,15.086,0l15.081-15.082C513.04,489.627,513.04,482.873,508.874,478.708z M202.667,362.667 c-88.229,0-160-71.771-160-160s71.771-160,160-160s160,71.771,160,160S290.896,362.667,202.667,362.667z"/> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>',
            'close': '<svg width="20" viewBox="0 0 513 513" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"> <g transform="matrix(0.589845,0,0,0.589845,105,105)"> <g> <path d="M284.286,256.002L506.143,34.144C513.954,26.333 513.954,13.669 506.143,5.859C498.332,-1.951 485.668,-1.952 477.858,5.859L256,227.717L34.143,5.859C26.332,-1.952 13.668,-1.952 5.858,5.859C-1.952,13.67 -1.953,26.334 5.858,34.144L227.715,256.001L5.858,477.859C-1.953,485.67 -1.953,498.334 5.858,506.144C9.763,510.049 14.882,512.001 20.001,512.001C25.12,512.001 30.238,510.049 34.144,506.144L256,284.287L477.857,506.144C481.762,510.049 486.881,512.001 492,512.001C497.119,512.001 502.237,510.049 506.143,506.144C513.954,498.333 513.954,485.669 506.143,477.859L284.286,256.002Z" style="fill-rule:nonzero;"/> </g> </g> </svg>',
        }
    }

    this.selectors = {
        emit: null,
        trigger: null,
        emojiPicker: '.fg-emoji-picker',
        emojiFooter: '.fg-emoji-picker-footer',
        emojiBody: '.fg-emoji-picker-all-categories',
        emojiHeader: '.fg-emoji-picker-categories',
        closeButton: '.fg-emoji-picker-close-button'
    }

    this.bindEvents = () => {
        this.lib('body').on('click', this.functions.removeEmojiPicker.bind(this));
        this.lib('body').on('click', this.functions.emitEmoji.bind(this));
        this.lib('body').on('click', this.functions.openEmojiSelector.bind(this), this.selectors.trigger);
        this.lib('body').on('input', this.functions.search.bind(this), this.selectors.search);
        // this.lib('body').on('keydown', this.functions.removeEmojiPicker.bind(this));
        this.lib('body').on('click', this.destroy.bind(this), this.selectors.closeButton);
        window.addEventListener('keydown', e => {
            if (e.keyCode === 27) {
                if (document.querySelector(this.selectors.emojiPicker)) {
                    document.querySelectorAll(this.selectors.emojiPicker).forEach(emoji => emoji.remove())
                    this.emojiItems = undefined
                }
            }

        })
    }

    this.html = {
        pickerBody: () => {
            return `<div class="fg-emoji-picker">
                <div style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                    <div>
                        <div class="sk-chase">
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                            <div class="sk-chase-dot"></div>
                        </div>
                    </div>
                </div>
            </div>`;
        }
    }

    this.destroy = () => {
        document.querySelectorAll(this.selectors.emojiPicker).forEach(p => p.remove())
        this.emojiItems = undefined;
        return true;
    }

    setCaretPosition = (field, caretPos) => {
		var elem = field
		if (elem != null) {
			if (elem.createTextRange) {
				var range = elem.createTextRange();
				range.move('character', caretPos);
				range.select();
			} else {
				if (elem.selectionStart) {
					elem.focus();
					elem.setSelectionRange(caretPos, caretPos);
				} else {
					elem.focus();
                }
			}
		}
	}

    this.functions = {

        // Put in place 
        putInPlace(myField, myValue) {
            if (document.selection) {
                myField.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
            } else if (myField.selectionStart || myField.selectionStart == "0") {
                const startPos = myField.selectionStart;
                const endPos = myField.selectionEnd;
                myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
                
                setCaretPosition(myField, startPos + 2)
                
            } else {
                myField.value += myValue;
                myField.focus()
            }
        },

        // Search
        search(e) {
            const val = e.target.value;
            if (!Array.isArray(this.emojiItems)) {
                this.emojiItems = Array.from(e.target.closest(this.selectors.emojiPicker).querySelectorAll(`${this.selectors.emojiBody} li`));
            }
            this.emojiItems.filter(emoji => {
                if (!emoji.getAttribute('data-name').match(val)) {
                    emoji.style.display = 'none'
                } else {
                    emoji.style.display = ''
                }
            })

            if (!val.length) this.emojiItems = undefined;
        },


        fetchEmojiData: () => {
            fetch(`${this.variable.dir}full-emoji-list.json`)
                .then(response => response.json())
                .then(object => { this.emojiJson = object });
        },

        generateStyle() {
            document.head.insertAdjacentHTML('beforeend', `
                <style>
                .sk-chase {
                    width: 40px;
                    height: 40px;
                    position: relative;
                    animation: sk-chase 2.5s infinite linear both;
                  }
                  
                  .sk-chase-dot {
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    left: 0;
                    top: 0; 
                    animation: sk-chase-dot 2.0s infinite ease-in-out both; 
                  }
                  
                  .sk-chase-dot:before {
                    content: '';
                    display: block;
                    width: 25%;
                    height: 25%;
                    background-color: #9fa1a5;
                    border-radius: 100%;
                    animation: sk-chase-dot-before 2.0s infinite ease-in-out both; 
                  }
                  
                  .sk-chase-dot:nth-child(1) { animation-delay: -1.1s; }
                  .sk-chase-dot:nth-child(2) { animation-delay: -1.0s; }
                  .sk-chase-dot:nth-child(3) { animation-delay: -0.9s; }
                  .sk-chase-dot:nth-child(4) { animation-delay: -0.8s; }
                  .sk-chase-dot:nth-child(5) { animation-delay: -0.7s; }
                  .sk-chase-dot:nth-child(6) { animation-delay: -0.6s; }
                  .sk-chase-dot:nth-child(1):before { animation-delay: -1.1s; }
                  .sk-chase-dot:nth-child(2):before { animation-delay: -1.0s; }
                  .sk-chase-dot:nth-child(3):before { animation-delay: -0.9s; }
                  .sk-chase-dot:nth-child(4):before { animation-delay: -0.8s; }
                  .sk-chase-dot:nth-child(5):before { animation-delay: -0.7s; }
                  .sk-chase-dot:nth-child(6):before { animation-delay: -0.6s; }
                  
                  @keyframes sk-chase {
                    100% { transform: rotate(360deg); } 
                  }
                  
                  @keyframes sk-chase-dot {
                    80%, 100% { transform: rotate(360deg); } 
                  }
                  
                  @keyframes sk-chase-dot-before {
                    50% {
                      transform: scale(0.4); 
                    } 100%, 0% {
                      transform: scale(1.0); 
                    } 
                  }


                a.fg-emoji-picker-close-button {
                    background-color: #dedede;
                }

                ${this.selectors.emojiPicker} {
                    /* position: fixed; */
                    position: absolute;
                    z-index: 999;
                    width: 300px;
                    min-height: 360px;
                    background-color: white;
                    box-shadow: 0px 2px 13px -2px rgba(0, 0, 0, 0.1803921568627451);
                    border-radius: 4px;
                    overflow: hidden;
                }

                ${this.selectors.emojiPicker} ${this.selectors.emojiBody} {
                    height: 301px;
                    overflow-y: auto;
                    padding: 0 15px 15px 15px;
                }

                ${this.selectors.emojiPicker} .fg-emoji-picker-container-title {
                    color: black;
                    margin: 10px 0;
                }

                ${this.selectors.emojiPicker} * {
                    margin: 0;
                    padding: 0;
                    text-decoration: none;
                    color: #666;
                    font-family: sans-serif;
                }

                ${this.selectors.emojiPicker} ul {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                }

                ${this.selectors.emojiPicker} .fg-emoji-picker-category {
                    margin-top: 1px;
                    padding-top: 15px;
                }

                .fg-emoji-picker-categories svg {
                    width: 17px;
                    height: 17px;
                }

                .fg-emoji-picker-grid {
                    display: flex;
                    flex-wrap: wrap;
                }

                .fg-emoji-picker-grid > li {
                    cursor: pointer;
                    flex: 0 0 calc(100% / 5);
                    max-width: calc(100% / 5);
                    height: 48px;
                    min-width: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    transition: all .2s ease;
                    background-color: white;
                }

                .fg-emoji-picker-grid > li:hover {
                    background-color: #99c9ef;
                }

                .fg-emoji-picker-grid > li:hover a {
                    -webkit-transform: scale(1.2);
                    -ms-transform: scale(1.2);
                    -moz-transform: scale(1.2);
                    transform: scale(1.2);
                }

                .fg-emoji-picker-grid > li > a {
                    display: block;
                    font-size: 25px;
                    margin: 0;
                    padding: 25px 0px;
                    line-height: 0;
                    -webkit-transition: all .3s ease;
                    -moz-transition: all .3s ease;
                    -ms-transition: all .3s ease;
                    transition: all .3s ease;
                }

                /* FILTERS */
                .fg-emoji-picker-categories {
                    /*padding: 0 15px;*/
                    background: #ececec;
                }

                .fg-emoji-picker-categories ul {
                    display: flex;
                    flex-wrap: wrap;
                }

                .fg-emoji-picker-categories li {
                    flex: 1;
                }

                .fg-emoji-picker-categories li.active {
                    background-color: #99c9ef;
                }

                .fg-emoji-picker-categories a {
                    padding: 12px 7px;
                    display: flex;
                    text-align: center;
                    justify-content: center;
                    align-items: center;
                    transition: all .2s ease;
                }

                .fg-emoji-picker-categories a:hover {
                    background-color: #99c9ef;
                }

                .fg-emoji-picker-search {
                    position: relative;
                    height: 25px;
                }

                .fg-emoji-picker-search input {
                    position: absolute;
                    width: 85%;
                    left: 0;
                    top: 0;
                    border: none;
                    padding: 5px 30px 5px 15px;
                    outline: none;
                    background-color: #dedede;
                    font-size: 12px;
                    color: #616161;
                }

                .fg-emoji-picker-search svg {
                    width: 15px;
                    height: 15px;
                    position: absolute;
                    right: 7px;
                    top: 5px;
                    fill: #333333;
                    pointer-events: none;
                }


                /* FOOTER */
                .fg-emoji-picker-footer {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: center;
                    height: 50px;
                    padding: 0 15px 15px 15px;
                }

                .fg-emoji-picker-footer-icon {
                    font-size: 30px;
                    margin-right: 8px;
                }
            </style>`)
        },

        removeEmojiPicker() {
            const el = window.event.target;
            const picker = document.querySelector(this.selectors.emojiPicker);

            if (!el.closest(this.selectors.emojiPicker)) picker ? picker.remove() : false;
            this.emojiItems = undefined
        },


        emitEmoji(e) {

            const el = e.target;

            if (el.tagName.toLowerCase() == 'a' && el.className.includes('fg-emoji-picker-item')) {
                e.preventDefault();

                let emoji = {
                    emoji: el.getAttribute('href'),
                    code: el.getAttribute('data-code')
                }
                if (this.variable.emit) this.variable.emit(emoji, this.triggerer)
                
                // If insert into option exists
                if (this.insertInto) this.functions.putInPlace(this.insertInto, emoji.emoji)

                if (this.variable.removeOnSelection) {
                    const picker = document.querySelector(this.selectors.emojiPicker);
                    picker.remove();
                }
            }

        },


        // Open omoji picker
        openEmojiSelector(e) {

            let el = e.target.closest(this.selectors.trigger)
            if (el) {
                e.preventDefault();

                // Bounding rect
                // Trigger position and (trigger) sizes
                let el = e.target.closest(this.selectors.trigger)

                if (typeof this.variable.emit === 'function') this.triggerer = el

                // Insert picker
                const emojiPickerMain = new DOMParser().parseFromString(this.html.pickerBody.apply(this), 'text/html').body.firstElementChild;
                document.body.insertAdjacentElement('afterbegin', emojiPickerMain);

                let positions = {
                    buttonTop: e.pageY,
                    buttonWidth: el.offsetWidth,
                    buttonFromLeft: el.getBoundingClientRect().left,
                    bodyHeight: document.body.offsetHeight,
                    bodyWidth: document.body.offsetWidth,
                    windowScrollPosition: window.pageYOffset,
                    emojiHeight: emojiPickerMain.offsetHeight,
                    emojiWidth: emojiPickerMain.offsetWidth,
                }


                // Element position object
                let position = {
                    top: emojiPickerMain.style.top = positions.buttonTop - positions.emojiHeight,
                    left: emojiPickerMain.style.left = positions.buttonFromLeft - positions.emojiWidth,
                    bottom: emojiPickerMain.style.top = positions.buttonTop,
                    right: emojiPickerMain.style.left = positions.buttonFromLeft + positions.buttonWidth
                }


                // Positioning emoji container top
                if (this.variable.position) {
                    this.variable.position.forEach(elemPos => {

                        if (elemPos === 'right') {
                            emojiPickerMain.style.left = position[elemPos] + 'px';
                        } else if (elemPos === 'bottom') {
                            emojiPickerMain.style.top = position[elemPos] + 'px';
                        } else {
                            emojiPickerMain.style[elemPos] = position[elemPos] + 'px';
                        }
                    })
                }



                // Emoji Picker Promise
                this.emojiPicker().then(emojiPicker => {

                    // Create node from 
                    const node = new DOMParser().parseFromString(emojiPicker, 'text/html').body.firstElementChild;
                    
                    emojiPickerMain.innerHTML = node.innerHTML;

                    const emojiFooter       = emojiPickerMain.querySelector(this.selectors.emojiFooter);
                    const emojiBody         = emojiPickerMain.querySelector(this.selectors.emojiBody)
                    const emojiPickerHeader = emojiPickerMain.querySelector(this.selectors.emojiHeader);

                    emojiPickerMain.querySelector('input').focus();

                    // Add event listener on click
                    document.body.querySelector(this.selectors.emojiPicker).onclick = function (e) {

                        e.preventDefault();

                        let scrollTo = (element, to, duration = 100) => {

                            if (duration <= 0) return;
                            var difference = to - element.scrollTop;
                            var perTick = difference / duration * 10;

                            setTimeout(function () {
                                element.scrollTop = element.scrollTop + perTick;
                                if (element.scrollTop === to) return;
                                scrollTo(element, to, duration - 10);
                            }, 10);
                        }

                        const el = e.target;
                        const filterLlnk = el.closest('a');

                        document.querySelectorAll('.fg-emoji-picker-categories li').forEach(item => item.classList.remove('active'))

                        if (filterLlnk && filterLlnk.closest('li') && filterLlnk.closest('li').getAttribute('data-index')) {

                            let list = filterLlnk.closest('li');
                            list.classList.add('active');
                            let listIndex = list.getAttribute('data-index');

                            scrollTo(emojiBody, emojiBody.querySelector(`#${listIndex}`).offsetTop - emojiPickerHeader.offsetHeight);
                        }
                    }

                })
            }
        }
    },



    // Create emoji container / Builder engine
    this.emojiPicker = () => {

        let picker = `
        <div class="fg-emoji-picker">
            <div class="fg-emoji-picker-categories">%categories%
                <div class="fg-emoji-picker-search">
                    <input placeholder="Search emoji" autofocus />
                    ${this.variable.categoryIcons.search}
                </div>
            </div>
            <div>%pickerContainer%</div>
        </div>`;

            const closeBtn      = this.variable.closeButton ? `<li><a class="fg-emoji-picker-close-button" href="#">${this.variable.categoryIcons.close}</a></li>` : '';
            let categories      = `<ul>%categories%${closeBtn}</ul>`;
            let categoriesInner = ``;
            let outerUl         = `<div class="fg-emoji-picker-all-categories">%outerUL%</div>`;
            let innerLists      = ``;

            let fetchData = null;
            if (this.emojiJson) {
                fetchData = new Promise((resolve, reject) => {
                    let index = 0;
                    // Loop through emoji object
                    let object = this.emojiJson;
                    for (const key in object) {
                        if (object.hasOwnProperty(key)) {

                            // Index count
                            index += 1;
                            let keyToId = key.split(' ').join('-').split('&').join('').toLowerCase();

                            const categories = object[key];
                            categoriesInner += `<li class="${index === 1 ? 'active' : ''}" id="${keyToId}" data-index="${keyToId}"><a href="#${keyToId}">${this.variable.categoryIcons[keyToId]}</a></li>`

                            innerLists += `
                            <ul class="fg-emoji-picker-category ${index === 1 ? 'active' : ''}" id="${keyToId}" category-name="${key}">
                                <div class="fg-emoji-picker-container-title">${key}</div>
                                <div class="fg-emoji-picker-grid">`;

                                    // Loop through emoji items
                                    categories.forEach(item => {
                                        innerLists += `<li data-name="${item.description.toLowerCase()}"><a class="fg-emoji-picker-item" title="${item.description}" data-name="${item.description.toLowerCase()}" data-code="${item.code}" href="${item.emoji}">${item.emoji}</a></li>`;
                                    });
                                    innerLists += `
                                </div>
                            </ul>`;
                        }
                    }
                    let allSmiles = outerUl.replace('%outerUL%', innerLists)
                    let cats = categories.replace('%categories%', categoriesInner);
                    let pickerContainer = picker.replace('%pickerContainer%', allSmiles)
                    let data = pickerContainer.replace('%categories%', cats);
                    resolve(data);
                });
            } else {
                fetchData = fetch(`${this.variable.dir}full-emoji-list.json`)
                    .then(response => response.json())
                    .then(object => {

                        // Index count
                        let index = 0;
                        this.emojiJson = object;

                        // Loop through emoji object
                        for (const key in object) {
                            if (object.hasOwnProperty(key)) {

                                // Index count
                                index += 1;

                                let keyToId = key.split(' ').join('-').split('&').join('').toLowerCase();

                                const categories = object[key];
                                categoriesInner += `<li class="${index === 1 ? 'active' : ''}" id="${keyToId}" data-index="${keyToId}"><a href="#${keyToId}">${this.variable.categoryIcons[keyToId]}</a></li>`

                                innerLists += `
                                <ul class="fg-emoji-picker-category ${index === 1 ? 'active' : ''}" id="${keyToId}" category-name="${key}">
                                    <div class="fg-emoji-picker-container-title">${key}</div>
                                    <div class="fg-emoji-picker-grid">`;

                                        // Loop through emoji items
                                        categories.forEach(item => {
                                            innerLists += `<li data-name="${item.description.toLowerCase()}"><a class="fg-emoji-picker-item" title="${item.description}" data-name="${item.description.toLowerCase()}" data-code="${item.code}" href="${item.emoji}">${item.emoji}</a></li>`;
                                        })

                                        innerLists += `
                                    </div>
                                </ul>`;
                            }
                        }


                        let allSmiles = outerUl.replace('%outerUL%', innerLists)
                        let cats = categories.replace('%categories%', categoriesInner);
                        let pickerContainer = picker.replace('%pickerContainer%', allSmiles)
                        let data = pickerContainer.replace('%categories%', cats);
                        return data;
                    })
            }

            return fetchData;
        }

    this.init();

}