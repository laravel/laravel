interface StringPathBookmark {
    start: string;
    end?: string;
    forward?: boolean;
}
interface RangeBookmark {
    rng: Range;
    forward?: boolean;
}
interface IdBookmark {
    id: string;
    keep?: boolean;
    forward?: boolean;
}
interface IndexBookmark {
    name: string;
    index: number;
}
interface PathBookmark {
    start: number[];
    end?: number[];
    isFakeCaret?: boolean;
    forward?: boolean;
}
declare type Bookmark = StringPathBookmark | RangeBookmark | IdBookmark | IndexBookmark | PathBookmark;
declare type NormalizedEvent<E, T = any> = E & {
    readonly type: string;
    readonly target: T;
    readonly isDefaultPrevented: () => boolean;
    readonly preventDefault: () => void;
    readonly isPropagationStopped: () => boolean;
    readonly stopPropagation: () => void;
    readonly isImmediatePropagationStopped: () => boolean;
    readonly stopImmediatePropagation: () => void;
};
declare type MappedEvent<T extends {}, K extends string> = K extends keyof T ? T[K] : any;
interface NativeEventMap {
    'beforepaste': Event;
    'blur': FocusEvent;
    'beforeinput': InputEvent;
    'click': MouseEvent;
    'compositionend': Event;
    'compositionstart': Event;
    'compositionupdate': Event;
    'contextmenu': PointerEvent;
    'copy': ClipboardEvent;
    'cut': ClipboardEvent;
    'dblclick': MouseEvent;
    'drag': DragEvent;
    'dragdrop': DragEvent;
    'dragend': DragEvent;
    'draggesture': DragEvent;
    'dragover': DragEvent;
    'dragstart': DragEvent;
    'drop': DragEvent;
    'focus': FocusEvent;
    'focusin': FocusEvent;
    'focusout': FocusEvent;
    'input': InputEvent;
    'keydown': KeyboardEvent;
    'keypress': KeyboardEvent;
    'keyup': KeyboardEvent;
    'mousedown': MouseEvent;
    'mouseenter': MouseEvent;
    'mouseleave': MouseEvent;
    'mousemove': MouseEvent;
    'mouseout': MouseEvent;
    'mouseover': MouseEvent;
    'mouseup': MouseEvent;
    'paste': ClipboardEvent;
    'selectionchange': Event;
    'submit': Event;
    'touchend': TouchEvent;
    'touchmove': TouchEvent;
    'touchstart': TouchEvent;
    'touchcancel': TouchEvent;
    'wheel': WheelEvent;
}
declare type EditorEvent<T> = NormalizedEvent<T>;
interface EventDispatcherSettings {
    scope?: any;
    toggleEvent?: (name: string, state: boolean) => void | boolean;
    beforeFire?: <T>(args: EditorEvent<T>) => void;
}
interface EventDispatcherConstructor<T extends {}> {
    readonly prototype: EventDispatcher<T>;
    new (settings?: EventDispatcherSettings): EventDispatcher<T>;
    isNative: (name: string) => boolean;
}
declare class EventDispatcher<T extends {}> {
    static isNative(name: string): boolean;
    private readonly settings;
    private readonly scope;
    private readonly toggleEvent;
    private bindings;
    constructor(settings?: EventDispatcherSettings);
    fire<K extends string, U extends MappedEvent<T, K>>(name: K, args?: U): EditorEvent<U>;
    dispatch<K extends string, U extends MappedEvent<T, K>>(name: K, args?: U): EditorEvent<U>;
    on<K extends string>(name: K, callback: false | ((event: EditorEvent<MappedEvent<T, K>>) => void | boolean), prepend?: boolean, extra?: {}): this;
    off<K extends string>(name?: K, callback?: (event: EditorEvent<MappedEvent<T, K>>) => void): this;
    once<K extends string>(name: K, callback: (event: EditorEvent<MappedEvent<T, K>>) => void, prepend?: boolean): this;
    has(name: string): boolean;
}
declare const enum UndoLevelType {
    Fragmented = "fragmented",
    Complete = "complete"
}
interface BaseUndoLevel {
    type: UndoLevelType;
    bookmark: Bookmark | null;
    beforeBookmark: Bookmark | null;
}
interface FragmentedUndoLevel extends BaseUndoLevel {
    type: UndoLevelType.Fragmented;
    fragments: string[];
    content: '';
}
interface CompleteUndoLevel extends BaseUndoLevel {
    type: UndoLevelType.Complete;
    fragments: null;
    content: string;
}
declare type NewUndoLevel = CompleteUndoLevel | FragmentedUndoLevel;
declare type UndoLevel = NewUndoLevel & {
    bookmark: Bookmark;
};
interface UndoManager {
    data: UndoLevel[];
    typing: boolean;
    add: (level?: Partial<UndoLevel>, event?: EditorEvent<any>) => UndoLevel | null;
    dispatchChange: () => void;
    beforeChange: () => void;
    undo: () => UndoLevel | undefined;
    redo: () => UndoLevel | undefined;
    clear: () => void;
    reset: () => void;
    hasUndo: () => boolean;
    hasRedo: () => boolean;
    transact: (callback: () => void) => UndoLevel | null;
    ignore: (callback: () => void) => void;
    extra: (callback1: () => void, callback2: () => void) => void;
}
declare type SchemaType = 'html4' | 'html5' | 'html5-strict';
interface ElementSettings {
    block_elements?: string;
    boolean_attributes?: string;
    move_caret_before_on_enter_elements?: string;
    non_empty_elements?: string;
    self_closing_elements?: string;
    text_block_elements?: string;
    text_inline_elements?: string;
    void_elements?: string;
    whitespace_elements?: string;
    transparent_elements?: string;
}
interface SchemaSettings extends ElementSettings {
    custom_elements?: string;
    extended_valid_elements?: string;
    invalid_elements?: string;
    invalid_styles?: string | Record<string, string>;
    schema?: SchemaType;
    valid_children?: string;
    valid_classes?: string | Record<string, string>;
    valid_elements?: string;
    valid_styles?: string | Record<string, string>;
    verify_html?: boolean;
    padd_empty_block_inline_children?: boolean;
}
interface Attribute {
    required?: boolean;
    defaultValue?: string;
    forcedValue?: string;
    validValues?: Record<string, {}>;
}
interface DefaultAttribute {
    name: string;
    value: string;
}
interface AttributePattern extends Attribute {
    pattern: RegExp;
}
interface ElementRule {
    attributes: Record<string, Attribute>;
    attributesDefault?: DefaultAttribute[];
    attributesForced?: DefaultAttribute[];
    attributesOrder: string[];
    attributePatterns?: AttributePattern[];
    attributesRequired?: string[];
    paddEmpty?: boolean;
    removeEmpty?: boolean;
    removeEmptyAttrs?: boolean;
    paddInEmptyBlock?: boolean;
}
interface SchemaElement extends ElementRule {
    outputName?: string;
    parentsRequired?: string[];
    pattern?: RegExp;
}
interface SchemaMap {
    [name: string]: {};
}
interface SchemaRegExpMap {
    [name: string]: RegExp;
}
interface Schema {
    type: SchemaType;
    children: Record<string, SchemaMap>;
    elements: Record<string, SchemaElement>;
    getValidStyles: () => Record<string, string[]> | undefined;
    getValidClasses: () => Record<string, SchemaMap> | undefined;
    getBlockElements: () => SchemaMap;
    getInvalidStyles: () => Record<string, SchemaMap> | undefined;
    getVoidElements: () => SchemaMap;
    getTextBlockElements: () => SchemaMap;
    getTextInlineElements: () => SchemaMap;
    getBoolAttrs: () => SchemaMap;
    getElementRule: (name: string) => SchemaElement | undefined;
    getSelfClosingElements: () => SchemaMap;
    getNonEmptyElements: () => SchemaMap;
    getMoveCaretBeforeOnEnterElements: () => SchemaMap;
    getWhitespaceElements: () => SchemaMap;
    getTransparentElements: () => SchemaMap;
    getSpecialElements: () => SchemaRegExpMap;
    isValidChild: (name: string, child: string) => boolean;
    isValid: (name: string, attr?: string) => boolean;
    getCustomElements: () => SchemaMap;
    addValidElements: (validElements: string) => void;
    setValidElements: (validElements: string) => void;
    addCustomElements: (customElements: string) => void;
    addValidChildren: (validChildren: any) => void;
}
declare type Attributes$1 = Array<{
    name: string;
    value: string;
}> & {
    map: Record<string, string>;
};
interface AstNodeConstructor {
    readonly prototype: AstNode;
    new (name: string, type: number): AstNode;
    create(name: string, attrs?: Record<string, string>): AstNode;
}
declare class AstNode {
    static create(name: string, attrs?: Record<string, string>): AstNode;
    name: string;
    type: number;
    attributes?: Attributes$1;
    value?: string;
    parent?: AstNode | null;
    firstChild?: AstNode | null;
    lastChild?: AstNode | null;
    next?: AstNode | null;
    prev?: AstNode | null;
    raw?: boolean;
    constructor(name: string, type: number);
    replace(node: AstNode): AstNode;
    attr(name: string, value: string | null | undefined): AstNode | undefined;
    attr(name: Record<string, string | null | undefined> | undefined): AstNode | undefined;
    attr(name: string): string | undefined;
    clone(): AstNode;
    wrap(wrapper: AstNode): AstNode;
    unwrap(): void;
    remove(): AstNode;
    append(node: AstNode): AstNode;
    insert(node: AstNode, refNode: AstNode, before?: boolean): AstNode;
    getAll(name: string): AstNode[];
    children(): AstNode[];
    empty(): AstNode;
    isEmpty(elements: SchemaMap, whitespace?: SchemaMap, predicate?: (node: AstNode) => boolean): boolean;
    walk(prev?: boolean): AstNode | null | undefined;
}
declare type Content = string | AstNode;
declare type ContentFormat = 'raw' | 'text' | 'html' | 'tree';
interface GetContentArgs {
    format: ContentFormat;
    get: boolean;
    getInner: boolean;
    no_events?: boolean;
    save?: boolean;
    source_view?: boolean;
    [key: string]: any;
}
interface SetContentArgs {
    format: string;
    set: boolean;
    content: Content;
    no_events?: boolean;
    no_selection?: boolean;
    paste?: boolean;
    load?: boolean;
    initial?: boolean;
    [key: string]: any;
}
interface GetSelectionContentArgs extends GetContentArgs {
    selection?: boolean;
    contextual?: boolean;
}
interface SetSelectionContentArgs extends SetContentArgs {
    content: string;
    selection?: boolean;
}
interface BlobInfoData {
    id?: string;
    name?: string;
    filename?: string;
    blob: Blob;
    base64: string;
    blobUri?: string;
    uri?: string;
}
interface BlobInfo {
    id: () => string;
    name: () => string;
    filename: () => string;
    blob: () => Blob;
    base64: () => string;
    blobUri: () => string;
    uri: () => string | undefined;
}
interface BlobCache {
    create: {
        (o: BlobInfoData): BlobInfo;
        (id: string, blob: Blob, base64: string, name?: string, filename?: string): BlobInfo;
    };
    add: (blobInfo: BlobInfo) => void;
    get: (id: string) => BlobInfo | undefined;
    getByUri: (blobUri: string) => BlobInfo | undefined;
    getByData: (base64: string, type: string) => BlobInfo | undefined;
    findFirst: (predicate: (blobInfo: BlobInfo) => boolean) => BlobInfo | undefined;
    removeByUri: (blobUri: string) => void;
    destroy: () => void;
}
interface BlobInfoImagePair {
    image: HTMLImageElement;
    blobInfo: BlobInfo;
}
declare class NodeChange {
    private readonly editor;
    private lastPath;
    constructor(editor: Editor);
    nodeChanged(args?: Record<string, any>): void;
    private isSameElementPath;
}
interface SelectionOverrides {
    showCaret: (direction: number, node: HTMLElement, before: boolean, scrollIntoView?: boolean) => Range | null;
    showBlockCaretContainer: (blockCaretContainer: HTMLElement) => void;
    hideFakeCaret: () => void;
    destroy: () => void;
}
interface Quirks {
    refreshContentEditable(): void;
    isHidden(): boolean;
}
declare type DecoratorData = Record<string, any>;
declare type Decorator = (uid: string, data: DecoratorData) => {
    attributes?: {};
    classes?: string[];
};
declare type AnnotationListener = (state: boolean, name: string, data?: {
    uid: string;
    nodes: any[];
}) => void;
declare type AnnotationListenerApi = AnnotationListener;
interface AnnotatorSettings {
    decorate: Decorator;
    persistent?: boolean;
}
interface Annotator {
    register: (name: string, settings: AnnotatorSettings) => void;
    annotate: (name: string, data: DecoratorData) => void;
    annotationChanged: (name: string, f: AnnotationListenerApi) => void;
    remove: (name: string) => void;
    removeAll: (name: string) => void;
    getAll: (name: string) => Record<string, Element[]>;
}
interface GeomRect {
    readonly x: number;
    readonly y: number;
    readonly w: number;
    readonly h: number;
}
interface Rect {
    inflate: (rect: GeomRect, w: number, h: number) => GeomRect;
    relativePosition: (rect: GeomRect, targetRect: GeomRect, rel: string) => GeomRect;
    findBestRelativePosition: (rect: GeomRect, targetRect: GeomRect, constrainRect: GeomRect, rels: string[]) => string | null;
    intersect: (rect: GeomRect, cropRect: GeomRect) => GeomRect | null;
    clamp: (rect: GeomRect, clampRect: GeomRect, fixedSize?: boolean) => GeomRect;
    create: (x: number, y: number, w: number, h: number) => GeomRect;
    fromClientRect: (clientRect: DOMRect) => GeomRect;
}
interface NotificationManagerImpl {
    open: (spec: NotificationSpec, closeCallback: () => void) => NotificationApi;
    close: <T extends NotificationApi>(notification: T) => void;
    getArgs: <T extends NotificationApi>(notification: T) => NotificationSpec;
}
interface NotificationSpec {
    type?: 'info' | 'warning' | 'error' | 'success';
    text: string;
    icon?: string;
    progressBar?: boolean;
    timeout?: number;
    closeButton?: boolean;
}
interface NotificationApi {
    close: () => void;
    progressBar: {
        value: (percent: number) => void;
    };
    text: (text: string) => void;
    reposition: () => void;
    getEl: () => HTMLElement;
    settings: NotificationSpec;
}
interface NotificationManager {
    open: (spec: NotificationSpec) => NotificationApi;
    close: () => void;
    getNotifications: () => NotificationApi[];
}
interface UploadFailure {
    message: string;
    remove?: boolean;
}
declare type ProgressFn = (percent: number) => void;
declare type UploadHandler = (blobInfo: BlobInfo, progress: ProgressFn) => Promise<string>;
interface UploadResult$2 {
    url: string;
    blobInfo: BlobInfo;
    status: boolean;
    error?: UploadFailure;
}
interface RawPattern {
    start?: any;
    end?: any;
    format?: any;
    cmd?: any;
    value?: any;
    replacement?: any;
}
interface InlineBasePattern {
    readonly start: string;
    readonly end: string;
}
interface InlineFormatPattern extends InlineBasePattern {
    readonly type: 'inline-format';
    readonly format: string[];
}
interface InlineCmdPattern extends InlineBasePattern {
    readonly type: 'inline-command';
    readonly cmd: string;
    readonly value?: any;
}
declare type InlinePattern = InlineFormatPattern | InlineCmdPattern;
interface BlockBasePattern {
    readonly start: string;
}
interface BlockFormatPattern extends BlockBasePattern {
    readonly type: 'block-format';
    readonly format: string;
}
interface BlockCmdPattern extends BlockBasePattern {
    readonly type: 'block-command';
    readonly cmd: string;
    readonly value?: any;
}
declare type BlockPattern = BlockFormatPattern | BlockCmdPattern;
declare type Pattern = InlinePattern | BlockPattern;
interface DynamicPatternContext {
    readonly text: string;
    readonly block: Element;
}
declare type DynamicPatternsLookup = (ctx: DynamicPatternContext) => Pattern[];
declare type RawDynamicPatternsLookup = (ctx: DynamicPatternContext) => RawPattern[];
interface AlertBannerSpec {
    type: 'alertbanner';
    level: 'info' | 'warn' | 'error' | 'success';
    text: string;
    icon: string;
    url?: string;
}
interface ButtonSpec {
    type: 'button';
    text: string;
    enabled?: boolean;
    primary?: boolean;
    name?: string;
    icon?: string;
    borderless?: boolean;
    buttonType?: 'primary' | 'secondary' | 'toolbar';
}
interface FormComponentSpec {
    type: string;
    name: string;
}
interface FormComponentWithLabelSpec extends FormComponentSpec {
    label?: string;
}
interface CheckboxSpec extends FormComponentSpec {
    type: 'checkbox';
    label: string;
    enabled?: boolean;
}
interface CollectionSpec extends FormComponentWithLabelSpec {
    type: 'collection';
}
interface CollectionItem {
    value: string;
    text: string;
    icon: string;
}
interface ColorInputSpec extends FormComponentWithLabelSpec {
    type: 'colorinput';
    storageKey?: string;
}
interface ColorPickerSpec extends FormComponentWithLabelSpec {
    type: 'colorpicker';
}
interface CustomEditorInit {
    setValue: (value: string) => void;
    getValue: () => string;
    destroy: () => void;
}
declare type CustomEditorInitFn = (elm: HTMLElement, settings: any) => Promise<CustomEditorInit>;
interface CustomEditorOldSpec extends FormComponentSpec {
    type: 'customeditor';
    tag?: string;
    init: (e: HTMLElement) => Promise<CustomEditorInit>;
}
interface CustomEditorNewSpec extends FormComponentSpec {
    type: 'customeditor';
    tag?: string;
    scriptId: string;
    scriptUrl: string;
    settings?: any;
}
declare type CustomEditorSpec = CustomEditorOldSpec | CustomEditorNewSpec;
interface DropZoneSpec extends FormComponentWithLabelSpec {
    type: 'dropzone';
}
interface GridSpec {
    type: 'grid';
    columns: number;
    items: BodyComponentSpec[];
}
interface HtmlPanelSpec {
    type: 'htmlpanel';
    html: string;
    presets?: 'presentation' | 'document';
}
interface IframeSpec extends FormComponentWithLabelSpec {
    type: 'iframe';
    sandboxed?: boolean;
    transparent?: boolean;
}
interface ImagePreviewSpec extends FormComponentSpec {
    type: 'imagepreview';
    height?: string;
}
interface InputSpec extends FormComponentWithLabelSpec {
    type: 'input';
    inputMode?: string;
    placeholder?: string;
    maximized?: boolean;
    enabled?: boolean;
}
interface LabelSpec {
    type: 'label';
    label: string;
    items: BodyComponentSpec[];
}
interface ListBoxSingleItemSpec {
    text: string;
    value: string;
}
interface ListBoxNestedItemSpec {
    text: string;
    items: ListBoxItemSpec[];
}
declare type ListBoxItemSpec = ListBoxNestedItemSpec | ListBoxSingleItemSpec;
interface ListBoxSpec extends FormComponentWithLabelSpec {
    type: 'listbox';
    items: ListBoxItemSpec[];
    disabled?: boolean;
}
interface PanelSpec {
    type: 'panel';
    classes?: string[];
    items: BodyComponentSpec[];
}
interface SelectBoxItemSpec {
    text: string;
    value: string;
}
interface SelectBoxSpec extends FormComponentWithLabelSpec {
    type: 'selectbox';
    items: SelectBoxItemSpec[];
    size?: number;
    enabled?: boolean;
}
interface SizeInputSpec extends FormComponentWithLabelSpec {
    type: 'sizeinput';
    constrain?: boolean;
    enabled?: boolean;
}
interface SliderSpec extends FormComponentSpec {
    type: 'slider';
    label: string;
    min?: number;
    max?: number;
}
interface TableSpec {
    type: 'table';
    header: string[];
    cells: string[][];
}
interface TextAreaSpec extends FormComponentWithLabelSpec {
    type: 'textarea';
    placeholder?: string;
    maximized?: boolean;
    enabled?: boolean;
}
interface UrlInputSpec extends FormComponentWithLabelSpec {
    type: 'urlinput';
    filetype?: 'image' | 'media' | 'file';
    enabled?: boolean;
}
interface UrlInputData {
    value: string;
    meta: {
        text?: string;
    };
}
declare type BodyComponentSpec = BarSpec | ButtonSpec | CheckboxSpec | TextAreaSpec | InputSpec | ListBoxSpec | SelectBoxSpec | SizeInputSpec | SliderSpec | IframeSpec | HtmlPanelSpec | UrlInputSpec | DropZoneSpec | ColorInputSpec | GridSpec | ColorPickerSpec | ImagePreviewSpec | AlertBannerSpec | CollectionSpec | LabelSpec | TableSpec | PanelSpec | CustomEditorSpec;
interface BarSpec {
    type: 'bar';
    items: BodyComponentSpec[];
}
interface CommonMenuItemSpec {
    enabled?: boolean;
    text?: string;
    value?: string;
    meta?: Record<string, any>;
    shortcut?: string;
}
interface CommonMenuItemInstanceApi {
    isEnabled: () => boolean;
    setEnabled: (state: boolean) => void;
}
interface DialogToggleMenuItemSpec extends CommonMenuItemSpec {
    type?: 'togglemenuitem';
    name: string;
}
declare type DialogFooterMenuButtonItemSpec = DialogToggleMenuItemSpec;
interface BaseDialogFooterButtonSpec {
    name?: string;
    align?: 'start' | 'end';
    primary?: boolean;
    enabled?: boolean;
    icon?: string;
    buttonType?: 'primary' | 'secondary';
}
interface DialogFooterNormalButtonSpec extends BaseDialogFooterButtonSpec {
    type: 'submit' | 'cancel' | 'custom';
    text: string;
}
interface DialogFooterMenuButtonSpec extends BaseDialogFooterButtonSpec {
    type: 'menu';
    text?: string;
    tooltip?: string;
    icon?: string;
    items: DialogFooterMenuButtonItemSpec[];
}
declare type DialogFooterButtonSpec = DialogFooterNormalButtonSpec | DialogFooterMenuButtonSpec;
interface TabSpec {
    name?: string;
    title: string;
    items: BodyComponentSpec[];
}
interface TabPanelSpec {
    type: 'tabpanel';
    tabs: TabSpec[];
}
declare type DialogDataItem = any;
declare type DialogData = Record<string, DialogDataItem>;
interface DialogInstanceApi<T extends DialogData> {
    getData: () => T;
    setData: (data: Partial<T>) => void;
    setEnabled: (name: string, state: boolean) => void;
    focus: (name: string) => void;
    showTab: (name: string) => void;
    redial: (nu: DialogSpec<T>) => void;
    block: (msg: string) => void;
    unblock: () => void;
    close: () => void;
}
interface DialogActionDetails {
    name: string;
    value?: any;
}
interface DialogChangeDetails<T> {
    name: keyof T;
}
interface DialogTabChangeDetails {
    newTabName: string;
    oldTabName: string;
}
declare type DialogActionHandler<T extends DialogData> = (api: DialogInstanceApi<T>, details: DialogActionDetails) => void;
declare type DialogChangeHandler<T extends DialogData> = (api: DialogInstanceApi<T>, details: DialogChangeDetails<T>) => void;
declare type DialogSubmitHandler<T extends DialogData> = (api: DialogInstanceApi<T>) => void;
declare type DialogCloseHandler = () => void;
declare type DialogCancelHandler<T extends DialogData> = (api: DialogInstanceApi<T>) => void;
declare type DialogTabChangeHandler<T extends DialogData> = (api: DialogInstanceApi<T>, details: DialogTabChangeDetails) => void;
declare type DialogSize = 'normal' | 'medium' | 'large';
interface DialogSpec<T extends DialogData> {
    title: string;
    size?: DialogSize;
    body: TabPanelSpec | PanelSpec;
    buttons: DialogFooterButtonSpec[];
    initialData?: Partial<T>;
    onAction?: DialogActionHandler<T>;
    onChange?: DialogChangeHandler<T>;
    onSubmit?: DialogSubmitHandler<T>;
    onClose?: DialogCloseHandler;
    onCancel?: DialogCancelHandler<T>;
    onTabChange?: DialogTabChangeHandler<T>;
}
interface UrlDialogInstanceApi {
    block: (msg: string) => void;
    unblock: () => void;
    close: () => void;
    sendMessage: (msg: any) => void;
}
interface UrlDialogActionDetails {
    name: string;
    value?: any;
}
interface UrlDialogMessage {
    mceAction: string;
    [key: string]: any;
}
declare type UrlDialogActionHandler = (api: UrlDialogInstanceApi, actions: UrlDialogActionDetails) => void;
declare type UrlDialogCloseHandler = () => void;
declare type UrlDialogCancelHandler = (api: UrlDialogInstanceApi) => void;
declare type UrlDialogMessageHandler = (api: UrlDialogInstanceApi, message: UrlDialogMessage) => void;
interface UrlDialogFooterButtonSpec extends DialogFooterNormalButtonSpec {
    type: 'cancel' | 'custom';
}
interface UrlDialogSpec {
    title: string;
    url: string;
    height?: number;
    width?: number;
    buttons?: UrlDialogFooterButtonSpec[];
    onAction?: UrlDialogActionHandler;
    onClose?: UrlDialogCloseHandler;
    onCancel?: UrlDialogCancelHandler;
    onMessage?: UrlDialogMessageHandler;
}
declare type CardContainerDirection = 'vertical' | 'horizontal';
declare type CardContainerAlign = 'left' | 'right';
declare type CardContainerValign = 'top' | 'middle' | 'bottom';
interface CardContainerSpec {
    type: 'cardcontainer';
    items: CardItemSpec[];
    direction?: CardContainerDirection;
    align?: CardContainerAlign;
    valign?: CardContainerValign;
}
interface CardImageSpec {
    type: 'cardimage';
    src: string;
    alt?: string;
    classes?: string[];
}
interface CardTextSpec {
    type: 'cardtext';
    text: string;
    name?: string;
    classes?: string[];
}
declare type CardItemSpec = CardContainerSpec | CardImageSpec | CardTextSpec;
interface CardMenuItemInstanceApi extends CommonMenuItemInstanceApi {
}
interface CardMenuItemSpec extends Omit<CommonMenuItemSpec, 'text' | 'shortcut'> {
    type: 'cardmenuitem';
    label?: string;
    items: CardItemSpec[];
    onSetup?: (api: CardMenuItemInstanceApi) => (api: CardMenuItemInstanceApi) => void;
    onAction?: (api: CardMenuItemInstanceApi) => void;
}
interface SeparatorMenuItemSpec {
    type?: 'separator';
    text?: string;
}
declare type ColumnTypes$1 = number | 'auto';
declare type SeparatorItemSpec = SeparatorMenuItemSpec;
interface AutocompleterItemSpec {
    type?: 'autocompleteitem';
    value: string;
    text?: string;
    icon?: string;
    meta?: Record<string, any>;
}
declare type AutocompleterContents = SeparatorItemSpec | AutocompleterItemSpec | CardMenuItemSpec;
interface AutocompleterSpec {
    type?: 'autocompleter';
    ch?: string;
    trigger?: string;
    minChars?: number;
    columns?: ColumnTypes$1;
    matches?: (rng: Range, text: string, pattern: string) => boolean;
    fetch: (pattern: string, maxResults: number, fetchOptions: Record<string, any>) => Promise<AutocompleterContents[]>;
    onAction: (autocompleterApi: AutocompleterInstanceApi, rng: Range, value: string, meta: Record<string, any>) => void;
    maxResults?: number;
    highlightOn?: string[];
}
interface AutocompleterInstanceApi {
    hide: () => void;
    reload: (fetchOptions: Record<string, any>) => void;
}
declare type ContextPosition = 'node' | 'selection' | 'line';
declare type ContextScope = 'node' | 'editor';
interface ContextBarSpec {
    predicate?: (elem: Element) => boolean;
    position?: ContextPosition;
    scope?: ContextScope;
}
interface BaseToolbarButtonSpec<I extends BaseToolbarButtonInstanceApi> {
    enabled?: boolean;
    tooltip?: string;
    icon?: string;
    text?: string;
    onSetup?: (api: I) => (api: I) => void;
}
interface BaseToolbarButtonInstanceApi {
    isEnabled: () => boolean;
    setEnabled: (state: boolean) => void;
}
interface ToolbarButtonSpec extends BaseToolbarButtonSpec<ToolbarButtonInstanceApi> {
    type?: 'button';
    onAction: (api: ToolbarButtonInstanceApi) => void;
}
interface ToolbarButtonInstanceApi extends BaseToolbarButtonInstanceApi {
}
interface BaseToolbarToggleButtonSpec<I extends BaseToolbarButtonInstanceApi> extends BaseToolbarButtonSpec<I> {
    active?: boolean;
}
interface BaseToolbarToggleButtonInstanceApi extends BaseToolbarButtonInstanceApi {
    isActive: () => boolean;
    setActive: (state: boolean) => void;
}
interface ToolbarToggleButtonSpec extends BaseToolbarToggleButtonSpec<ToolbarToggleButtonInstanceApi> {
    type?: 'togglebutton';
    onAction: (api: ToolbarToggleButtonInstanceApi) => void;
}
interface ToolbarToggleButtonInstanceApi extends BaseToolbarToggleButtonInstanceApi {
}
interface ContextFormLaunchButtonApi extends BaseToolbarButtonSpec<BaseToolbarButtonInstanceApi> {
    type: 'contextformbutton';
}
interface ContextFormLaunchToggleButtonSpec extends BaseToolbarToggleButtonSpec<BaseToolbarToggleButtonInstanceApi> {
    type: 'contextformtogglebutton';
}
interface ContextFormButtonInstanceApi extends BaseToolbarButtonInstanceApi {
}
interface ContextFormToggleButtonInstanceApi extends BaseToolbarToggleButtonInstanceApi {
}
interface ContextFormButtonSpec extends BaseToolbarButtonSpec<ContextFormButtonInstanceApi> {
    type?: 'contextformbutton';
    primary?: boolean;
    onAction: (formApi: ContextFormInstanceApi, api: ContextFormButtonInstanceApi) => void;
}
interface ContextFormToggleButtonSpec extends BaseToolbarToggleButtonSpec<ContextFormToggleButtonInstanceApi> {
    type?: 'contextformtogglebutton';
    onAction: (formApi: ContextFormInstanceApi, buttonApi: ContextFormToggleButtonInstanceApi) => void;
    primary?: boolean;
}
interface ContextFormInstanceApi {
    hide: () => void;
    getValue: () => string;
}
interface ContextFormSpec extends ContextBarSpec {
    type?: 'contextform';
    initValue?: () => string;
    label?: string;
    launch?: ContextFormLaunchButtonApi | ContextFormLaunchToggleButtonSpec;
    commands: Array<ContextFormToggleButtonSpec | ContextFormButtonSpec>;
}
interface ContextToolbarSpec extends ContextBarSpec {
    type?: 'contexttoolbar';
    items: string;
}
interface ChoiceMenuItemSpec extends CommonMenuItemSpec {
    type?: 'choiceitem';
    icon?: string;
}
interface ChoiceMenuItemInstanceApi extends CommonMenuItemInstanceApi {
    isActive: () => boolean;
    setActive: (state: boolean) => void;
}
interface ContextMenuItem extends CommonMenuItemSpec {
    text: string;
    icon?: string;
    type?: 'item';
    onAction: () => void;
}
interface ContextSubMenu extends CommonMenuItemSpec {
    type: 'submenu';
    text: string;
    icon?: string;
    getSubmenuItems: () => string | Array<ContextMenuContents>;
}
declare type ContextMenuContents = string | ContextMenuItem | SeparatorMenuItemSpec | ContextSubMenu;
interface ContextMenuApi {
    update: (element: Element) => string | Array<ContextMenuContents>;
}
interface FancyActionArgsMap {
    'inserttable': {
        numRows: number;
        numColumns: number;
    };
    'colorswatch': {
        value: string;
    };
}
interface BaseFancyMenuItemSpec<T extends keyof FancyActionArgsMap> {
    type: 'fancymenuitem';
    fancytype: T;
    initData?: Record<string, unknown>;
    onAction?: (data: FancyActionArgsMap[T]) => void;
}
interface InsertTableMenuItemSpec extends BaseFancyMenuItemSpec<'inserttable'> {
    fancytype: 'inserttable';
    initData?: {};
}
interface ColorSwatchMenuItemSpec extends BaseFancyMenuItemSpec<'colorswatch'> {
    fancytype: 'colorswatch';
    initData?: {
        allowCustomColors?: boolean;
        colors?: ChoiceMenuItemSpec[];
        storageKey?: string;
    };
}
declare type FancyMenuItemSpec = InsertTableMenuItemSpec | ColorSwatchMenuItemSpec;
interface MenuItemSpec extends CommonMenuItemSpec {
    type?: 'menuitem';
    icon?: string;
    onSetup?: (api: MenuItemInstanceApi) => (api: MenuItemInstanceApi) => void;
    onAction?: (api: MenuItemInstanceApi) => void;
}
interface MenuItemInstanceApi extends CommonMenuItemInstanceApi {
}
declare type NestedMenuItemContents = string | MenuItemSpec | NestedMenuItemSpec | ToggleMenuItemSpec | SeparatorMenuItemSpec | FancyMenuItemSpec;
interface NestedMenuItemSpec extends CommonMenuItemSpec {
    type?: 'nestedmenuitem';
    icon?: string;
    getSubmenuItems: () => string | Array<NestedMenuItemContents>;
    onSetup?: (api: NestedMenuItemInstanceApi) => (api: NestedMenuItemInstanceApi) => void;
}
interface NestedMenuItemInstanceApi extends CommonMenuItemInstanceApi {
}
interface ToggleMenuItemSpec extends CommonMenuItemSpec {
    type?: 'togglemenuitem';
    icon?: string;
    active?: boolean;
    onSetup?: (api: ToggleMenuItemInstanceApi) => void;
    onAction: (api: ToggleMenuItemInstanceApi) => void;
}
interface ToggleMenuItemInstanceApi extends CommonMenuItemInstanceApi {
    isActive: () => boolean;
    setActive: (state: boolean) => void;
}
type PublicDialog_d_AlertBannerSpec = AlertBannerSpec;
type PublicDialog_d_BarSpec = BarSpec;
type PublicDialog_d_BodyComponentSpec = BodyComponentSpec;
type PublicDialog_d_ButtonSpec = ButtonSpec;
type PublicDialog_d_CheckboxSpec = CheckboxSpec;
type PublicDialog_d_CollectionItem = CollectionItem;
type PublicDialog_d_CollectionSpec = CollectionSpec;
type PublicDialog_d_ColorInputSpec = ColorInputSpec;
type PublicDialog_d_ColorPickerSpec = ColorPickerSpec;
type PublicDialog_d_CustomEditorSpec = CustomEditorSpec;
type PublicDialog_d_CustomEditorInit = CustomEditorInit;
type PublicDialog_d_CustomEditorInitFn = CustomEditorInitFn;
type PublicDialog_d_DialogData = DialogData;
type PublicDialog_d_DialogSize = DialogSize;
type PublicDialog_d_DialogSpec<T extends DialogData> = DialogSpec<T>;
type PublicDialog_d_DialogInstanceApi<T extends DialogData> = DialogInstanceApi<T>;
type PublicDialog_d_DialogFooterButtonSpec = DialogFooterButtonSpec;
type PublicDialog_d_DialogActionDetails = DialogActionDetails;
type PublicDialog_d_DialogChangeDetails<T> = DialogChangeDetails<T>;
type PublicDialog_d_DialogTabChangeDetails = DialogTabChangeDetails;
type PublicDialog_d_DropZoneSpec = DropZoneSpec;
type PublicDialog_d_GridSpec = GridSpec;
type PublicDialog_d_HtmlPanelSpec = HtmlPanelSpec;
type PublicDialog_d_IframeSpec = IframeSpec;
type PublicDialog_d_ImagePreviewSpec = ImagePreviewSpec;
type PublicDialog_d_InputSpec = InputSpec;
type PublicDialog_d_LabelSpec = LabelSpec;
type PublicDialog_d_ListBoxSpec = ListBoxSpec;
type PublicDialog_d_ListBoxItemSpec = ListBoxItemSpec;
type PublicDialog_d_ListBoxNestedItemSpec = ListBoxNestedItemSpec;
type PublicDialog_d_ListBoxSingleItemSpec = ListBoxSingleItemSpec;
type PublicDialog_d_PanelSpec = PanelSpec;
type PublicDialog_d_SelectBoxSpec = SelectBoxSpec;
type PublicDialog_d_SelectBoxItemSpec = SelectBoxItemSpec;
type PublicDialog_d_SizeInputSpec = SizeInputSpec;
type PublicDialog_d_SliderSpec = SliderSpec;
type PublicDialog_d_TableSpec = TableSpec;
type PublicDialog_d_TabSpec = TabSpec;
type PublicDialog_d_TabPanelSpec = TabPanelSpec;
type PublicDialog_d_TextAreaSpec = TextAreaSpec;
type PublicDialog_d_UrlInputData = UrlInputData;
type PublicDialog_d_UrlInputSpec = UrlInputSpec;
type PublicDialog_d_UrlDialogSpec = UrlDialogSpec;
type PublicDialog_d_UrlDialogFooterButtonSpec = UrlDialogFooterButtonSpec;
type PublicDialog_d_UrlDialogInstanceApi = UrlDialogInstanceApi;
type PublicDialog_d_UrlDialogActionDetails = UrlDialogActionDetails;
type PublicDialog_d_UrlDialogMessage = UrlDialogMessage;
declare namespace PublicDialog_d {
    export { PublicDialog_d_AlertBannerSpec as AlertBannerSpec, PublicDialog_d_BarSpec as BarSpec, PublicDialog_d_BodyComponentSpec as BodyComponentSpec, PublicDialog_d_ButtonSpec as ButtonSpec, PublicDialog_d_CheckboxSpec as CheckboxSpec, PublicDialog_d_CollectionItem as CollectionItem, PublicDialog_d_CollectionSpec as CollectionSpec, PublicDialog_d_ColorInputSpec as ColorInputSpec, PublicDialog_d_ColorPickerSpec as ColorPickerSpec, PublicDialog_d_CustomEditorSpec as CustomEditorSpec, PublicDialog_d_CustomEditorInit as CustomEditorInit, PublicDialog_d_CustomEditorInitFn as CustomEditorInitFn, PublicDialog_d_DialogData as DialogData, PublicDialog_d_DialogSize as DialogSize, PublicDialog_d_DialogSpec as DialogSpec, PublicDialog_d_DialogInstanceApi as DialogInstanceApi, PublicDialog_d_DialogFooterButtonSpec as DialogFooterButtonSpec, PublicDialog_d_DialogActionDetails as DialogActionDetails, PublicDialog_d_DialogChangeDetails as DialogChangeDetails, PublicDialog_d_DialogTabChangeDetails as DialogTabChangeDetails, PublicDialog_d_DropZoneSpec as DropZoneSpec, PublicDialog_d_GridSpec as GridSpec, PublicDialog_d_HtmlPanelSpec as HtmlPanelSpec, PublicDialog_d_IframeSpec as IframeSpec, PublicDialog_d_ImagePreviewSpec as ImagePreviewSpec, PublicDialog_d_InputSpec as InputSpec, PublicDialog_d_LabelSpec as LabelSpec, PublicDialog_d_ListBoxSpec as ListBoxSpec, PublicDialog_d_ListBoxItemSpec as ListBoxItemSpec, PublicDialog_d_ListBoxNestedItemSpec as ListBoxNestedItemSpec, PublicDialog_d_ListBoxSingleItemSpec as ListBoxSingleItemSpec, PublicDialog_d_PanelSpec as PanelSpec, PublicDialog_d_SelectBoxSpec as SelectBoxSpec, PublicDialog_d_SelectBoxItemSpec as SelectBoxItemSpec, PublicDialog_d_SizeInputSpec as SizeInputSpec, PublicDialog_d_SliderSpec as SliderSpec, PublicDialog_d_TableSpec as TableSpec, PublicDialog_d_TabSpec as TabSpec, PublicDialog_d_TabPanelSpec as TabPanelSpec, PublicDialog_d_TextAreaSpec as TextAreaSpec, PublicDialog_d_UrlInputData as UrlInputData, PublicDialog_d_UrlInputSpec as UrlInputSpec, PublicDialog_d_UrlDialogSpec as UrlDialogSpec, PublicDialog_d_UrlDialogFooterButtonSpec as UrlDialogFooterButtonSpec, PublicDialog_d_UrlDialogInstanceApi as UrlDialogInstanceApi, PublicDialog_d_UrlDialogActionDetails as UrlDialogActionDetails, PublicDialog_d_UrlDialogMessage as UrlDialogMessage, };
}
type PublicInlineContent_d_AutocompleterSpec = AutocompleterSpec;
type PublicInlineContent_d_AutocompleterItemSpec = AutocompleterItemSpec;
type PublicInlineContent_d_AutocompleterContents = AutocompleterContents;
type PublicInlineContent_d_AutocompleterInstanceApi = AutocompleterInstanceApi;
type PublicInlineContent_d_ContextPosition = ContextPosition;
type PublicInlineContent_d_ContextScope = ContextScope;
type PublicInlineContent_d_ContextFormSpec = ContextFormSpec;
type PublicInlineContent_d_ContextFormInstanceApi = ContextFormInstanceApi;
type PublicInlineContent_d_ContextFormButtonSpec = ContextFormButtonSpec;
type PublicInlineContent_d_ContextFormButtonInstanceApi = ContextFormButtonInstanceApi;
type PublicInlineContent_d_ContextFormToggleButtonSpec = ContextFormToggleButtonSpec;
type PublicInlineContent_d_ContextFormToggleButtonInstanceApi = ContextFormToggleButtonInstanceApi;
type PublicInlineContent_d_ContextToolbarSpec = ContextToolbarSpec;
type PublicInlineContent_d_SeparatorItemSpec = SeparatorItemSpec;
declare namespace PublicInlineContent_d {
    export { PublicInlineContent_d_AutocompleterSpec as AutocompleterSpec, PublicInlineContent_d_AutocompleterItemSpec as AutocompleterItemSpec, PublicInlineContent_d_AutocompleterContents as AutocompleterContents, PublicInlineContent_d_AutocompleterInstanceApi as AutocompleterInstanceApi, PublicInlineContent_d_ContextPosition as ContextPosition, PublicInlineContent_d_ContextScope as ContextScope, PublicInlineContent_d_ContextFormSpec as ContextFormSpec, PublicInlineContent_d_ContextFormInstanceApi as ContextFormInstanceApi, PublicInlineContent_d_ContextFormButtonSpec as ContextFormButtonSpec, PublicInlineContent_d_ContextFormButtonInstanceApi as ContextFormButtonInstanceApi, PublicInlineContent_d_ContextFormToggleButtonSpec as ContextFormToggleButtonSpec, PublicInlineContent_d_ContextFormToggleButtonInstanceApi as ContextFormToggleButtonInstanceApi, PublicInlineContent_d_ContextToolbarSpec as ContextToolbarSpec, PublicInlineContent_d_SeparatorItemSpec as SeparatorItemSpec, };
}
type PublicMenu_d_MenuItemSpec = MenuItemSpec;
type PublicMenu_d_MenuItemInstanceApi = MenuItemInstanceApi;
type PublicMenu_d_NestedMenuItemContents = NestedMenuItemContents;
type PublicMenu_d_NestedMenuItemSpec = NestedMenuItemSpec;
type PublicMenu_d_NestedMenuItemInstanceApi = NestedMenuItemInstanceApi;
type PublicMenu_d_FancyMenuItemSpec = FancyMenuItemSpec;
type PublicMenu_d_ColorSwatchMenuItemSpec = ColorSwatchMenuItemSpec;
type PublicMenu_d_InsertTableMenuItemSpec = InsertTableMenuItemSpec;
type PublicMenu_d_ToggleMenuItemSpec = ToggleMenuItemSpec;
type PublicMenu_d_ToggleMenuItemInstanceApi = ToggleMenuItemInstanceApi;
type PublicMenu_d_ChoiceMenuItemSpec = ChoiceMenuItemSpec;
type PublicMenu_d_ChoiceMenuItemInstanceApi = ChoiceMenuItemInstanceApi;
type PublicMenu_d_SeparatorMenuItemSpec = SeparatorMenuItemSpec;
type PublicMenu_d_ContextMenuApi = ContextMenuApi;
type PublicMenu_d_ContextMenuContents = ContextMenuContents;
type PublicMenu_d_ContextMenuItem = ContextMenuItem;
type PublicMenu_d_ContextSubMenu = ContextSubMenu;
type PublicMenu_d_CardMenuItemSpec = CardMenuItemSpec;
type PublicMenu_d_CardMenuItemInstanceApi = CardMenuItemInstanceApi;
type PublicMenu_d_CardItemSpec = CardItemSpec;
type PublicMenu_d_CardContainerSpec = CardContainerSpec;
type PublicMenu_d_CardImageSpec = CardImageSpec;
type PublicMenu_d_CardTextSpec = CardTextSpec;
declare namespace PublicMenu_d {
    export { PublicMenu_d_MenuItemSpec as MenuItemSpec, PublicMenu_d_MenuItemInstanceApi as MenuItemInstanceApi, PublicMenu_d_NestedMenuItemContents as NestedMenuItemContents, PublicMenu_d_NestedMenuItemSpec as NestedMenuItemSpec, PublicMenu_d_NestedMenuItemInstanceApi as NestedMenuItemInstanceApi, PublicMenu_d_FancyMenuItemSpec as FancyMenuItemSpec, PublicMenu_d_ColorSwatchMenuItemSpec as ColorSwatchMenuItemSpec, PublicMenu_d_InsertTableMenuItemSpec as InsertTableMenuItemSpec, PublicMenu_d_ToggleMenuItemSpec as ToggleMenuItemSpec, PublicMenu_d_ToggleMenuItemInstanceApi as ToggleMenuItemInstanceApi, PublicMenu_d_ChoiceMenuItemSpec as ChoiceMenuItemSpec, PublicMenu_d_ChoiceMenuItemInstanceApi as ChoiceMenuItemInstanceApi, PublicMenu_d_SeparatorMenuItemSpec as SeparatorMenuItemSpec, PublicMenu_d_ContextMenuApi as ContextMenuApi, PublicMenu_d_ContextMenuContents as ContextMenuContents, PublicMenu_d_ContextMenuItem as ContextMenuItem, PublicMenu_d_ContextSubMenu as ContextSubMenu, PublicMenu_d_CardMenuItemSpec as CardMenuItemSpec, PublicMenu_d_CardMenuItemInstanceApi as CardMenuItemInstanceApi, PublicMenu_d_CardItemSpec as CardItemSpec, PublicMenu_d_CardContainerSpec as CardContainerSpec, PublicMenu_d_CardImageSpec as CardImageSpec, PublicMenu_d_CardTextSpec as CardTextSpec, };
}
interface SidebarInstanceApi {
    element: () => HTMLElement;
}
interface SidebarSpec {
    icon?: string;
    tooltip?: string;
    onShow?: (api: SidebarInstanceApi) => void;
    onSetup?: (api: SidebarInstanceApi) => (api: SidebarInstanceApi) => void;
    onHide?: (api: SidebarInstanceApi) => void;
}
type PublicSidebar_d_SidebarSpec = SidebarSpec;
type PublicSidebar_d_SidebarInstanceApi = SidebarInstanceApi;
declare namespace PublicSidebar_d {
    export { PublicSidebar_d_SidebarSpec as SidebarSpec, PublicSidebar_d_SidebarInstanceApi as SidebarInstanceApi, };
}
interface ToolbarGroupSetting {
    name: string;
    items: string[];
}
declare type ToolbarConfig = string | ToolbarGroupSetting[];
interface GroupToolbarButtonInstanceApi extends BaseToolbarButtonInstanceApi {
}
interface GroupToolbarButtonSpec extends BaseToolbarButtonSpec<GroupToolbarButtonInstanceApi> {
    type?: 'grouptoolbarbutton';
    items?: ToolbarConfig;
}
declare type MenuButtonItemTypes = NestedMenuItemContents;
declare type SuccessCallback$1 = (menu: string | MenuButtonItemTypes[]) => void;
interface MenuButtonFetchContext {
    pattern: string;
}
interface BaseMenuButtonSpec {
    text?: string;
    tooltip?: string;
    icon?: string;
    search?: boolean | {
        placeholder?: string;
    };
    fetch: (success: SuccessCallback$1, fetchContext: MenuButtonFetchContext) => void;
    onSetup?: (api: BaseMenuButtonInstanceApi) => (api: BaseMenuButtonInstanceApi) => void;
}
interface BaseMenuButtonInstanceApi {
    isEnabled: () => boolean;
    setEnabled: (state: boolean) => void;
    isActive: () => boolean;
    setActive: (state: boolean) => void;
}
interface ToolbarMenuButtonSpec extends BaseMenuButtonSpec {
    type?: 'menubutton';
    onSetup?: (api: ToolbarMenuButtonInstanceApi) => (api: ToolbarMenuButtonInstanceApi) => void;
}
interface ToolbarMenuButtonInstanceApi extends BaseMenuButtonInstanceApi {
}
declare type ToolbarSplitButtonItemTypes = ChoiceMenuItemSpec | SeparatorMenuItemSpec;
declare type SuccessCallback = (menu: ToolbarSplitButtonItemTypes[]) => void;
declare type SelectPredicate = (value: string) => boolean;
declare type PresetTypes = 'color' | 'normal' | 'listpreview';
declare type ColumnTypes = number | 'auto';
interface ToolbarSplitButtonSpec {
    type?: 'splitbutton';
    tooltip?: string;
    icon?: string;
    text?: string;
    select?: SelectPredicate;
    presets?: PresetTypes;
    columns?: ColumnTypes;
    fetch: (success: SuccessCallback) => void;
    onSetup?: (api: ToolbarSplitButtonInstanceApi) => (api: ToolbarSplitButtonInstanceApi) => void;
    onAction: (api: ToolbarSplitButtonInstanceApi) => void;
    onItemAction: (api: ToolbarSplitButtonInstanceApi, value: string) => void;
}
interface ToolbarSplitButtonInstanceApi {
    isEnabled: () => boolean;
    setEnabled: (state: boolean) => void;
    setIconFill: (id: string, value: string) => void;
    isActive: () => boolean;
    setActive: (state: boolean) => void;
}
type PublicToolbar_d_ToolbarButtonSpec = ToolbarButtonSpec;
type PublicToolbar_d_ToolbarButtonInstanceApi = ToolbarButtonInstanceApi;
type PublicToolbar_d_ToolbarSplitButtonSpec = ToolbarSplitButtonSpec;
type PublicToolbar_d_ToolbarSplitButtonInstanceApi = ToolbarSplitButtonInstanceApi;
type PublicToolbar_d_ToolbarMenuButtonSpec = ToolbarMenuButtonSpec;
type PublicToolbar_d_ToolbarMenuButtonInstanceApi = ToolbarMenuButtonInstanceApi;
type PublicToolbar_d_ToolbarToggleButtonSpec = ToolbarToggleButtonSpec;
type PublicToolbar_d_ToolbarToggleButtonInstanceApi = ToolbarToggleButtonInstanceApi;
type PublicToolbar_d_GroupToolbarButtonSpec = GroupToolbarButtonSpec;
type PublicToolbar_d_GroupToolbarButtonInstanceApi = GroupToolbarButtonInstanceApi;
declare namespace PublicToolbar_d {
    export { PublicToolbar_d_ToolbarButtonSpec as ToolbarButtonSpec, PublicToolbar_d_ToolbarButtonInstanceApi as ToolbarButtonInstanceApi, PublicToolbar_d_ToolbarSplitButtonSpec as ToolbarSplitButtonSpec, PublicToolbar_d_ToolbarSplitButtonInstanceApi as ToolbarSplitButtonInstanceApi, PublicToolbar_d_ToolbarMenuButtonSpec as ToolbarMenuButtonSpec, PublicToolbar_d_ToolbarMenuButtonInstanceApi as ToolbarMenuButtonInstanceApi, PublicToolbar_d_ToolbarToggleButtonSpec as ToolbarToggleButtonSpec, PublicToolbar_d_ToolbarToggleButtonInstanceApi as ToolbarToggleButtonInstanceApi, PublicToolbar_d_GroupToolbarButtonSpec as GroupToolbarButtonSpec, PublicToolbar_d_GroupToolbarButtonInstanceApi as GroupToolbarButtonInstanceApi, };
}
interface ViewNormalButtonSpec {
    type: 'button';
    text: string;
    buttonType?: 'primary' | 'secondary';
    onAction: () => void;
}
declare type ViewButtonSpec = ViewNormalButtonSpec;
interface ViewInstanceApi {
    getContainer: () => HTMLElement;
}
interface ViewSpec {
    buttons?: ViewButtonSpec[];
    onShow: (api: ViewInstanceApi) => void;
    onHide: (api: ViewInstanceApi) => void;
}
type PublicView_d_ViewSpec = ViewSpec;
type PublicView_d_ViewInstanceApi = ViewInstanceApi;
declare namespace PublicView_d {
    export { PublicView_d_ViewSpec as ViewSpec, PublicView_d_ViewInstanceApi as ViewInstanceApi, };
}
interface Registry$1 {
    addButton: (name: string, spec: ToolbarButtonSpec) => void;
    addGroupToolbarButton: (name: string, spec: GroupToolbarButtonSpec) => void;
    addToggleButton: (name: string, spec: ToolbarToggleButtonSpec) => void;
    addMenuButton: (name: string, spec: ToolbarMenuButtonSpec) => void;
    addSplitButton: (name: string, spec: ToolbarSplitButtonSpec) => void;
    addMenuItem: (name: string, spec: MenuItemSpec) => void;
    addNestedMenuItem: (name: string, spec: NestedMenuItemSpec) => void;
    addToggleMenuItem: (name: string, spec: ToggleMenuItemSpec) => void;
    addContextMenu: (name: string, spec: ContextMenuApi) => void;
    addContextToolbar: (name: string, spec: ContextToolbarSpec) => void;
    addContextForm: (name: string, spec: ContextFormSpec) => void;
    addIcon: (name: string, svgData: string) => void;
    addAutocompleter: (name: string, spec: AutocompleterSpec) => void;
    addSidebar: (name: string, spec: SidebarSpec) => void;
    addView: (name: string, spec: ViewSpec) => void;
    getAll: () => {
        buttons: Record<string, ToolbarButtonSpec | GroupToolbarButtonSpec | ToolbarMenuButtonSpec | ToolbarSplitButtonSpec | ToolbarToggleButtonSpec>;
        menuItems: Record<string, MenuItemSpec | NestedMenuItemSpec | ToggleMenuItemSpec>;
        popups: Record<string, AutocompleterSpec>;
        contextMenus: Record<string, ContextMenuApi>;
        contextToolbars: Record<string, ContextToolbarSpec | ContextFormSpec>;
        icons: Record<string, string>;
        sidebars: Record<string, SidebarSpec>;
        views: Record<string, ViewSpec>;
    };
}
interface AutocompleteLookupData {
    readonly matchText: string;
    readonly items: AutocompleterContents[];
    readonly columns: ColumnTypes$1;
    readonly onAction: (autoApi: AutocompleterInstanceApi, rng: Range, value: string, meta: Record<string, any>) => void;
    readonly highlightOn: string[];
}
interface AutocompleterEventArgs {
    readonly lookupData: AutocompleteLookupData[];
}
interface RangeLikeObject {
    startContainer: Node;
    startOffset: number;
    endContainer: Node;
    endOffset: number;
}
declare type ApplyFormat = BlockFormat | InlineFormat | SelectorFormat;
declare type RemoveFormat = RemoveBlockFormat | RemoveInlineFormat | RemoveSelectorFormat;
declare type Format = ApplyFormat | RemoveFormat;
declare type Formats = Record<string, Format | Format[]>;
declare type FormatAttrOrStyleValue = string | ((vars?: FormatVars) => string | null);
declare type FormatVars = Record<string, string | null>;
interface BaseFormat<T> {
    ceFalseOverride?: boolean;
    classes?: string | string[];
    collapsed?: boolean;
    exact?: boolean;
    expand?: boolean;
    links?: boolean;
    mixed?: boolean;
    block_expand?: boolean;
    onmatch?: (node: Element, fmt: T, itemName: string) => boolean;
    remove?: 'none' | 'empty' | 'all';
    remove_similar?: boolean;
    split?: boolean;
    deep?: boolean;
    preserve_attributes?: string[];
}
interface Block {
    block: string;
    list_block?: string;
    wrapper?: boolean;
}
interface Inline {
    inline: string;
}
interface Selector {
    selector: string;
    inherit?: boolean;
}
interface CommonFormat<T> extends BaseFormat<T> {
    attributes?: Record<string, FormatAttrOrStyleValue>;
    styles?: Record<string, FormatAttrOrStyleValue>;
    toggle?: boolean;
    preview?: string | false;
    onformat?: (elm: Element, fmt: T, vars?: FormatVars, node?: Node | RangeLikeObject | null) => void;
    clear_child_styles?: boolean;
    merge_siblings?: boolean;
    merge_with_parents?: boolean;
}
interface BlockFormat extends Block, CommonFormat<BlockFormat> {
}
interface InlineFormat extends Inline, CommonFormat<InlineFormat> {
}
interface SelectorFormat extends Selector, CommonFormat<SelectorFormat> {
}
interface CommonRemoveFormat<T> extends BaseFormat<T> {
    attributes?: string[] | Record<string, FormatAttrOrStyleValue>;
    styles?: string[] | Record<string, FormatAttrOrStyleValue>;
}
interface RemoveBlockFormat extends Block, CommonRemoveFormat<RemoveBlockFormat> {
}
interface RemoveInlineFormat extends Inline, CommonRemoveFormat<RemoveInlineFormat> {
}
interface RemoveSelectorFormat extends Selector, CommonRemoveFormat<RemoveSelectorFormat> {
}
interface Filter<C extends Function> {
    name: string;
    callbacks: C[];
}
interface ParserArgs {
    getInner?: boolean | number;
    forced_root_block?: boolean | string;
    context?: string;
    isRootContent?: boolean;
    format?: string;
    invalid?: boolean;
    no_events?: boolean;
    [key: string]: any;
}
declare type ParserFilterCallback = (nodes: AstNode[], name: string, args: ParserArgs) => void;
interface ParserFilter extends Filter<ParserFilterCallback> {
}
interface DomParserSettings {
    allow_html_data_urls?: boolean;
    allow_svg_data_urls?: boolean;
    allow_conditional_comments?: boolean;
    allow_html_in_named_anchor?: boolean;
    allow_script_urls?: boolean;
    allow_unsafe_link_target?: boolean;
    convert_fonts_to_spans?: boolean;
    fix_list_elements?: boolean;
    font_size_legacy_values?: string;
    forced_root_block?: boolean | string;
    forced_root_block_attrs?: Record<string, string>;
    preserve_cdata?: boolean;
    remove_trailing_brs?: boolean;
    root_name?: string;
    validate?: boolean;
    inline_styles?: boolean;
    blob_cache?: BlobCache;
    document?: Document;
}
interface DomParser {
    schema: Schema;
    addAttributeFilter: (name: string, callback: ParserFilterCallback) => void;
    getAttributeFilters: () => ParserFilter[];
    removeAttributeFilter: (name: string, callback?: ParserFilterCallback) => void;
    addNodeFilter: (name: string, callback: ParserFilterCallback) => void;
    getNodeFilters: () => ParserFilter[];
    removeNodeFilter: (name: string, callback?: ParserFilterCallback) => void;
    parse: (html: string, args?: ParserArgs) => AstNode;
}
interface StyleSheetLoaderSettings {
    maxLoadTime?: number;
    contentCssCors?: boolean;
    referrerPolicy?: ReferrerPolicy;
}
interface StyleSheetLoader {
    load: (url: string) => Promise<void>;
    loadAll: (urls: string[]) => Promise<string[]>;
    unload: (url: string) => void;
    unloadAll: (urls: string[]) => void;
    _setReferrerPolicy: (referrerPolicy: ReferrerPolicy) => void;
    _setContentCssCors: (contentCssCors: boolean) => void;
}
declare type Registry = Registry$1;
interface EditorUiApi {
    show: () => void;
    hide: () => void;
    setEnabled: (state: boolean) => void;
    isEnabled: () => boolean;
}
interface EditorUi extends EditorUiApi {
    registry: Registry;
    styleSheetLoader: StyleSheetLoader;
}
type Ui_d_Registry = Registry;
type Ui_d_EditorUiApi = EditorUiApi;
type Ui_d_EditorUi = EditorUi;
declare namespace Ui_d {
    export { Ui_d_Registry as Registry, PublicDialog_d as Dialog, PublicInlineContent_d as InlineContent, PublicMenu_d as Menu, PublicView_d as View, PublicSidebar_d as Sidebar, PublicToolbar_d as Toolbar, Ui_d_EditorUiApi as EditorUiApi, Ui_d_EditorUi as EditorUi, };
}
interface WindowParams {
    readonly inline?: 'cursor' | 'toolbar';
    readonly ariaAttrs?: boolean;
}
declare type InstanceApi<T extends DialogData> = UrlDialogInstanceApi | DialogInstanceApi<T>;
interface WindowManagerImpl {
    open: <T extends DialogData>(config: DialogSpec<T>, params: WindowParams | undefined, closeWindow: (dialog: DialogInstanceApi<T>) => void) => DialogInstanceApi<T>;
    openUrl: (config: UrlDialogSpec, closeWindow: (dialog: UrlDialogInstanceApi) => void) => UrlDialogInstanceApi;
    alert: (message: string, callback: () => void) => void;
    confirm: (message: string, callback: (state: boolean) => void) => void;
    close: (dialog: InstanceApi<any>) => void;
}
interface WindowManager {
    open: <T extends DialogData>(config: DialogSpec<T>, params?: WindowParams) => DialogInstanceApi<T>;
    openUrl: (config: UrlDialogSpec) => UrlDialogInstanceApi;
    alert: (message: string, callback?: () => void, scope?: any) => void;
    confirm: (message: string, callback?: (state: boolean) => void, scope?: any) => void;
    close: () => void;
}
interface ExecCommandEvent {
    command: string;
    ui: boolean;
    value?: any;
}
interface BeforeGetContentEvent extends GetContentArgs {
    selection?: boolean;
}
interface GetContentEvent extends BeforeGetContentEvent {
    content: string;
}
interface BeforeSetContentEvent extends SetContentArgs {
    content: string;
    selection?: boolean;
}
interface SetContentEvent extends BeforeSetContentEvent {
    content: string;
}
interface SaveContentEvent extends GetContentEvent {
    save: boolean;
}
interface NewBlockEvent {
    newBlock: Element;
}
interface NodeChangeEvent {
    element: Element;
    parents: Node[];
    selectionChange?: boolean;
    initial?: boolean;
}
interface FormatEvent {
    format: string;
    vars?: FormatVars;
    node?: Node | RangeLikeObject | null;
}
interface ObjectResizeEvent {
    target: HTMLElement;
    width: number;
    height: number;
    origin: string;
}
interface ObjectSelectedEvent {
    target: Node;
    targetClone?: Node;
}
interface ScrollIntoViewEvent {
    elm: HTMLElement;
    alignToTop: boolean | undefined;
}
interface SetSelectionRangeEvent {
    range: Range;
    forward: boolean | undefined;
}
interface ShowCaretEvent {
    target: Node;
    direction: number;
    before: boolean;
}
interface SwitchModeEvent {
    mode: string;
}
interface ChangeEvent {
    level: UndoLevel;
    lastLevel: UndoLevel | undefined;
}
interface AddUndoEvent extends ChangeEvent {
    originalEvent: Event | undefined;
}
interface UndoRedoEvent {
    level: UndoLevel;
}
interface WindowEvent<T extends DialogData> {
    dialog: InstanceApi<T>;
}
interface ProgressStateEvent {
    state: boolean;
    time?: number;
}
interface AfterProgressStateEvent {
    state: boolean;
}
interface PlaceholderToggleEvent {
    state: boolean;
}
interface LoadErrorEvent {
    message: string;
}
interface PreProcessEvent extends ParserArgs {
    node: Element;
}
interface PostProcessEvent extends ParserArgs {
    content: string;
}
interface PastePlainTextToggleEvent {
    state: boolean;
}
interface PastePreProcessEvent {
    content: string;
    readonly internal: boolean;
}
interface PastePostProcessEvent {
    node: HTMLElement;
    readonly internal: boolean;
}
interface NewTableRowEvent {
    node: HTMLTableRowElement;
}
interface NewTableCellEvent {
    node: HTMLTableCellElement;
}
interface TableEventData {
    readonly structure: boolean;
    readonly style: boolean;
}
interface TableModifiedEvent extends TableEventData {
    readonly table: HTMLTableElement;
}
interface BeforeOpenNotificationEvent {
    notification: NotificationSpec;
}
interface OpenNotificationEvent {
    notification: NotificationApi;
}
interface EditorEventMap extends Omit<NativeEventMap, 'blur' | 'focus'> {
    'activate': {
        relatedTarget: Editor | null;
    };
    'deactivate': {
        relatedTarget: Editor;
    };
    'focus': {
        blurredEditor: Editor | null;
    };
    'blur': {
        focusedEditor: Editor | null;
    };
    'resize': UIEvent;
    'scroll': UIEvent;
    'detach': {};
    'remove': {};
    'init': {};
    'ScrollIntoView': ScrollIntoViewEvent;
    'AfterScrollIntoView': ScrollIntoViewEvent;
    'ObjectResized': ObjectResizeEvent;
    'ObjectResizeStart': ObjectResizeEvent;
    'SwitchMode': SwitchModeEvent;
    'ScrollWindow': Event;
    'ResizeWindow': UIEvent;
    'SkinLoaded': {};
    'SkinLoadError': LoadErrorEvent;
    'PluginLoadError': LoadErrorEvent;
    'ModelLoadError': LoadErrorEvent;
    'IconsLoadError': LoadErrorEvent;
    'ThemeLoadError': LoadErrorEvent;
    'LanguageLoadError': LoadErrorEvent;
    'BeforeExecCommand': ExecCommandEvent;
    'ExecCommand': ExecCommandEvent;
    'NodeChange': NodeChangeEvent;
    'FormatApply': FormatEvent;
    'FormatRemove': FormatEvent;
    'ShowCaret': ShowCaretEvent;
    'SelectionChange': {};
    'ObjectSelected': ObjectSelectedEvent;
    'BeforeObjectSelected': ObjectSelectedEvent;
    'GetSelectionRange': {
        range: Range;
    };
    'SetSelectionRange': SetSelectionRangeEvent;
    'AfterSetSelectionRange': SetSelectionRangeEvent;
    'BeforeGetContent': BeforeGetContentEvent;
    'GetContent': GetContentEvent;
    'BeforeSetContent': BeforeSetContentEvent;
    'SetContent': SetContentEvent;
    'SaveContent': SaveContentEvent;
    'RawSaveContent': SaveContentEvent;
    'LoadContent': {
        load: boolean;
        element: HTMLElement;
    };
    'PreviewFormats': {};
    'AfterPreviewFormats': {};
    'ScriptsLoaded': {};
    'PreInit': {};
    'PostRender': {};
    'NewBlock': NewBlockEvent;
    'ClearUndos': {};
    'TypingUndo': {};
    'Redo': UndoRedoEvent;
    'Undo': UndoRedoEvent;
    'BeforeAddUndo': AddUndoEvent;
    'AddUndo': AddUndoEvent;
    'change': ChangeEvent;
    'CloseWindow': WindowEvent<any>;
    'OpenWindow': WindowEvent<any>;
    'ProgressState': ProgressStateEvent;
    'AfterProgressState': AfterProgressStateEvent;
    'PlaceholderToggle': PlaceholderToggleEvent;
    'tap': TouchEvent;
    'longpress': TouchEvent;
    'longpresscancel': {};
    'PreProcess': PreProcessEvent;
    'PostProcess': PostProcessEvent;
    'AutocompleterStart': AutocompleterEventArgs;
    'AutocompleterUpdate': AutocompleterEventArgs;
    'AutocompleterEnd': {};
    'PastePlainTextToggle': PastePlainTextToggleEvent;
    'PastePreProcess': PastePreProcessEvent;
    'PastePostProcess': PastePostProcessEvent;
    'TableModified': TableModifiedEvent;
    'NewRow': NewTableRowEvent;
    'NewCell': NewTableCellEvent;
    'SetAttrib': SetAttribEvent;
    'hide': {};
    'show': {};
    'dirty': {};
    'BeforeOpenNotification': BeforeOpenNotificationEvent;
    'OpenNotification': OpenNotificationEvent;
}
interface EditorManagerEventMap {
    'AddEditor': {
        editor: Editor;
    };
    'RemoveEditor': {
        editor: Editor;
    };
    'BeforeUnload': {
        returnValue: any;
    };
}
type EventTypes_d_ExecCommandEvent = ExecCommandEvent;
type EventTypes_d_BeforeGetContentEvent = BeforeGetContentEvent;
type EventTypes_d_GetContentEvent = GetContentEvent;
type EventTypes_d_BeforeSetContentEvent = BeforeSetContentEvent;
type EventTypes_d_SetContentEvent = SetContentEvent;
type EventTypes_d_SaveContentEvent = SaveContentEvent;
type EventTypes_d_NewBlockEvent = NewBlockEvent;
type EventTypes_d_NodeChangeEvent = NodeChangeEvent;
type EventTypes_d_FormatEvent = FormatEvent;
type EventTypes_d_ObjectResizeEvent = ObjectResizeEvent;
type EventTypes_d_ObjectSelectedEvent = ObjectSelectedEvent;
type EventTypes_d_ScrollIntoViewEvent = ScrollIntoViewEvent;
type EventTypes_d_SetSelectionRangeEvent = SetSelectionRangeEvent;
type EventTypes_d_ShowCaretEvent = ShowCaretEvent;
type EventTypes_d_SwitchModeEvent = SwitchModeEvent;
type EventTypes_d_ChangeEvent = ChangeEvent;
type EventTypes_d_AddUndoEvent = AddUndoEvent;
type EventTypes_d_UndoRedoEvent = UndoRedoEvent;
type EventTypes_d_WindowEvent<T extends DialogData> = WindowEvent<T>;
type EventTypes_d_ProgressStateEvent = ProgressStateEvent;
type EventTypes_d_AfterProgressStateEvent = AfterProgressStateEvent;
type EventTypes_d_PlaceholderToggleEvent = PlaceholderToggleEvent;
type EventTypes_d_LoadErrorEvent = LoadErrorEvent;
type EventTypes_d_PreProcessEvent = PreProcessEvent;
type EventTypes_d_PostProcessEvent = PostProcessEvent;
type EventTypes_d_PastePlainTextToggleEvent = PastePlainTextToggleEvent;
type EventTypes_d_PastePreProcessEvent = PastePreProcessEvent;
type EventTypes_d_PastePostProcessEvent = PastePostProcessEvent;
type EventTypes_d_NewTableRowEvent = NewTableRowEvent;
type EventTypes_d_NewTableCellEvent = NewTableCellEvent;
type EventTypes_d_TableEventData = TableEventData;
type EventTypes_d_TableModifiedEvent = TableModifiedEvent;
type EventTypes_d_BeforeOpenNotificationEvent = BeforeOpenNotificationEvent;
type EventTypes_d_OpenNotificationEvent = OpenNotificationEvent;
type EventTypes_d_EditorEventMap = EditorEventMap;
type EventTypes_d_EditorManagerEventMap = EditorManagerEventMap;
declare namespace EventTypes_d {
    export { EventTypes_d_ExecCommandEvent as ExecCommandEvent, EventTypes_d_BeforeGetContentEvent as BeforeGetContentEvent, EventTypes_d_GetContentEvent as GetContentEvent, EventTypes_d_BeforeSetContentEvent as BeforeSetContentEvent, EventTypes_d_SetContentEvent as SetContentEvent, EventTypes_d_SaveContentEvent as SaveContentEvent, EventTypes_d_NewBlockEvent as NewBlockEvent, EventTypes_d_NodeChangeEvent as NodeChangeEvent, EventTypes_d_FormatEvent as FormatEvent, EventTypes_d_ObjectResizeEvent as ObjectResizeEvent, EventTypes_d_ObjectSelectedEvent as ObjectSelectedEvent, EventTypes_d_ScrollIntoViewEvent as ScrollIntoViewEvent, EventTypes_d_SetSelectionRangeEvent as SetSelectionRangeEvent, EventTypes_d_ShowCaretEvent as ShowCaretEvent, EventTypes_d_SwitchModeEvent as SwitchModeEvent, EventTypes_d_ChangeEvent as ChangeEvent, EventTypes_d_AddUndoEvent as AddUndoEvent, EventTypes_d_UndoRedoEvent as UndoRedoEvent, EventTypes_d_WindowEvent as WindowEvent, EventTypes_d_ProgressStateEvent as ProgressStateEvent, EventTypes_d_AfterProgressStateEvent as AfterProgressStateEvent, EventTypes_d_PlaceholderToggleEvent as PlaceholderToggleEvent, EventTypes_d_LoadErrorEvent as LoadErrorEvent, EventTypes_d_PreProcessEvent as PreProcessEvent, EventTypes_d_PostProcessEvent as PostProcessEvent, EventTypes_d_PastePlainTextToggleEvent as PastePlainTextToggleEvent, EventTypes_d_PastePreProcessEvent as PastePreProcessEvent, EventTypes_d_PastePostProcessEvent as PastePostProcessEvent, EventTypes_d_NewTableRowEvent as NewTableRowEvent, EventTypes_d_NewTableCellEvent as NewTableCellEvent, EventTypes_d_TableEventData as TableEventData, EventTypes_d_TableModifiedEvent as TableModifiedEvent, EventTypes_d_BeforeOpenNotificationEvent as BeforeOpenNotificationEvent, EventTypes_d_OpenNotificationEvent as OpenNotificationEvent, EventTypes_d_EditorEventMap as EditorEventMap, EventTypes_d_EditorManagerEventMap as EditorManagerEventMap, };
}
type Format_d_Formats = Formats;
type Format_d_Format = Format;
type Format_d_ApplyFormat = ApplyFormat;
type Format_d_BlockFormat = BlockFormat;
type Format_d_InlineFormat = InlineFormat;
type Format_d_SelectorFormat = SelectorFormat;
type Format_d_RemoveFormat = RemoveFormat;
type Format_d_RemoveBlockFormat = RemoveBlockFormat;
type Format_d_RemoveInlineFormat = RemoveInlineFormat;
type Format_d_RemoveSelectorFormat = RemoveSelectorFormat;
declare namespace Format_d {
    export { Format_d_Formats as Formats, Format_d_Format as Format, Format_d_ApplyFormat as ApplyFormat, Format_d_BlockFormat as BlockFormat, Format_d_InlineFormat as InlineFormat, Format_d_SelectorFormat as SelectorFormat, Format_d_RemoveFormat as RemoveFormat, Format_d_RemoveBlockFormat as RemoveBlockFormat, Format_d_RemoveInlineFormat as RemoveInlineFormat, Format_d_RemoveSelectorFormat as RemoveSelectorFormat, };
}
declare type StyleFormat = BlockStyleFormat | InlineStyleFormat | SelectorStyleFormat;
declare type AllowedFormat = Separator | FormatReference | StyleFormat | NestedFormatting;
interface Separator {
    title: string;
}
interface FormatReference {
    title: string;
    format: string;
    icon?: string;
}
interface NestedFormatting {
    title: string;
    items: Array<FormatReference | StyleFormat>;
}
interface CommonStyleFormat {
    name?: string;
    title: string;
    icon?: string;
}
interface BlockStyleFormat extends BlockFormat, CommonStyleFormat {
}
interface InlineStyleFormat extends InlineFormat, CommonStyleFormat {
}
interface SelectorStyleFormat extends SelectorFormat, CommonStyleFormat {
}
declare type EntityEncoding = 'named' | 'numeric' | 'raw' | 'named,numeric' | 'named+numeric' | 'numeric,named' | 'numeric+named';
interface ContentLanguage {
    readonly title: string;
    readonly code: string;
    readonly customCode?: string;
}
declare type ThemeInitFunc = (editor: Editor, elm: HTMLElement) => {
    editorContainer: HTMLElement;
    iframeContainer: HTMLElement;
    height?: number;
    iframeHeight?: number;
    api?: EditorUiApi;
};
declare type SetupCallback = (editor: Editor) => void;
declare type FilePickerCallback = (callback: (value: string, meta?: Record<string, any>) => void, value: string, meta: Record<string, any>) => void;
declare type FilePickerValidationStatus = 'valid' | 'unknown' | 'invalid' | 'none';
declare type FilePickerValidationCallback = (info: {
    type: string;
    url: string;
}, callback: (validation: {
    status: FilePickerValidationStatus;
    message: string;
}) => void) => void;
declare type PastePreProcessFn = (editor: Editor, args: PastePreProcessEvent) => void;
declare type PastePostProcessFn = (editor: Editor, args: PastePostProcessEvent) => void;
declare type URLConverter = (url: string, name: string, elm?: string | Element) => string;
declare type URLConverterCallback = (url: string, node: Node | string | undefined, on_save: boolean, name: string) => string;
interface ToolbarGroup {
    name?: string;
    items: string[];
}
declare type ToolbarMode = 'floating' | 'sliding' | 'scrolling' | 'wrap';
declare type ToolbarLocation = 'top' | 'bottom' | 'auto';
interface BaseEditorOptions {
    a11y_advanced_options?: boolean;
    add_form_submit_trigger?: boolean;
    add_unload_trigger?: boolean;
    allow_conditional_comments?: boolean;
    allow_html_data_urls?: boolean;
    allow_html_in_named_anchor?: boolean;
    allow_script_urls?: boolean;
    allow_svg_data_urls?: boolean;
    allow_unsafe_link_target?: boolean;
    anchor_bottom?: false | string;
    anchor_top?: false | string;
    auto_focus?: string | true;
    automatic_uploads?: boolean;
    base_url?: string;
    block_formats?: string;
    block_unsupported_drop?: boolean;
    body_id?: string;
    body_class?: string;
    br_in_pre?: boolean;
    br_newline_selector?: string;
    browser_spellcheck?: boolean;
    branding?: boolean;
    cache_suffix?: string;
    color_cols?: number;
    color_cols_foreground?: number;
    color_cols_background?: number;
    color_map?: string[];
    color_map_foreground?: string[];
    color_map_background?: string[];
    color_default_foreground?: string;
    color_default_background?: string;
    content_css?: boolean | string | string[];
    content_css_cors?: boolean;
    content_security_policy?: string;
    content_style?: string;
    content_langs?: ContentLanguage[];
    contextmenu?: string | string[] | false;
    contextmenu_never_use_native?: boolean;
    convert_fonts_to_spans?: boolean;
    convert_urls?: boolean;
    custom_colors?: boolean;
    custom_elements?: string;
    custom_ui_selector?: string;
    custom_undo_redo_levels?: number;
    deprecation_warnings?: boolean;
    directionality?: 'ltr' | 'rtl';
    doctype?: string;
    document_base_url?: string;
    draggable_modal?: boolean;
    editable_class?: string;
    element_format?: 'xhtml' | 'html';
    elementpath?: boolean;
    encoding?: string;
    end_container_on_empty_block?: boolean | string;
    entities?: string;
    entity_encoding?: EntityEncoding;
    extended_valid_elements?: string;
    event_root?: string;
    file_picker_callback?: FilePickerCallback;
    file_picker_types?: string;
    file_picker_validator_handler?: FilePickerValidationCallback;
    fix_list_elements?: boolean;
    fixed_toolbar_container?: string;
    fixed_toolbar_container_target?: HTMLElement;
    font_css?: string | string[];
    font_family_formats?: string;
    font_size_classes?: string;
    font_size_legacy_values?: string;
    font_size_style_values?: string;
    font_size_formats?: string;
    forced_root_block?: string;
    forced_root_block_attrs?: Record<string, string>;
    formats?: Formats;
    format_noneditable_selector?: string;
    height?: number | string;
    hidden_input?: boolean;
    icons?: string;
    icons_url?: string;
    id?: string;
    iframe_aria_text?: string;
    iframe_attrs?: Record<string, string>;
    images_file_types?: string;
    images_replace_blob_uris?: boolean;
    images_reuse_filename?: boolean;
    images_upload_base_path?: string;
    images_upload_credentials?: boolean;
    images_upload_handler?: UploadHandler;
    images_upload_url?: string;
    indent?: boolean;
    indent_after?: string;
    indent_before?: string;
    indent_use_margin?: boolean;
    indentation?: string;
    init_instance_callback?: SetupCallback;
    inline?: boolean;
    inline_boundaries?: boolean;
    inline_boundaries_selector?: string;
    inline_styles?: boolean;
    invalid_elements?: string;
    invalid_styles?: string | Record<string, string>;
    keep_styles?: boolean;
    language?: string;
    language_load?: boolean;
    language_url?: string;
    line_height_formats?: string;
    max_height?: number;
    max_width?: number;
    menu?: Record<string, {
        title: string;
        items: string;
    }>;
    menubar?: boolean | string;
    min_height?: number;
    min_width?: number;
    model?: string;
    model_url?: string;
    newline_behavior?: 'block' | 'linebreak' | 'invert' | 'default';
    no_newline_selector?: string;
    noneditable_class?: string;
    noneditable_regexp?: RegExp | RegExp[];
    nowrap?: boolean;
    object_resizing?: boolean | string;
    paste_as_text?: boolean;
    paste_block_drop?: boolean;
    paste_data_images?: boolean;
    paste_merge_formats?: boolean;
    paste_postprocess?: PastePostProcessFn;
    paste_preprocess?: PastePreProcessFn;
    paste_remove_styles_if_webkit?: boolean;
    paste_tab_spaces?: number;
    paste_webkit_styles?: string;
    placeholder?: string;
    preserve_cdata?: boolean;
    preview_styles?: false | string;
    promotion?: boolean;
    protect?: RegExp[];
    readonly?: boolean;
    referrer_policy?: ReferrerPolicy;
    relative_urls?: boolean;
    remove_script_host?: boolean;
    remove_trailing_brs?: boolean;
    removed_menuitems?: string;
    resize?: boolean | 'both';
    resize_img_proportional?: boolean;
    root_name?: string;
    schema?: SchemaType;
    selector?: string;
    setup?: SetupCallback;
    sidebar_show?: string;
    skin?: boolean | string;
    skin_url?: string;
    smart_paste?: boolean;
    statusbar?: boolean;
    style_formats?: AllowedFormat[];
    style_formats_autohide?: boolean;
    style_formats_merge?: boolean;
    submit_patch?: boolean;
    suffix?: string;
    table_tab_navigation?: boolean;
    target?: HTMLElement;
    text_patterns?: RawPattern[] | false;
    text_patterns_lookup?: RawDynamicPatternsLookup;
    theme?: string | ThemeInitFunc | false;
    theme_url?: string;
    toolbar?: boolean | string | string[] | Array<ToolbarGroup>;
    toolbar1?: string;
    toolbar2?: string;
    toolbar3?: string;
    toolbar4?: string;
    toolbar5?: string;
    toolbar6?: string;
    toolbar7?: string;
    toolbar8?: string;
    toolbar9?: string;
    toolbar_groups?: Record<string, GroupToolbarButtonSpec>;
    toolbar_location?: ToolbarLocation;
    toolbar_mode?: ToolbarMode;
    toolbar_sticky?: boolean;
    toolbar_sticky_offset?: number;
    typeahead_urls?: boolean;
    url_converter?: URLConverter;
    url_converter_scope?: any;
    urlconverter_callback?: URLConverterCallback;
    valid_children?: string;
    valid_classes?: string | Record<string, string>;
    valid_elements?: string;
    valid_styles?: string | Record<string, string>;
    verify_html?: boolean;
    visual?: boolean;
    visual_anchor_class?: string;
    visual_table_class?: string;
    width?: number | string;
    disable_nodechange?: boolean;
    forced_plugins?: string | string[];
    plugin_base_urls?: Record<string, string>;
    service_message?: string;
    [key: string]: any;
}
interface RawEditorOptions extends BaseEditorOptions {
    external_plugins?: Record<string, string>;
    mobile?: RawEditorOptions;
    plugins?: string | string[];
}
interface NormalizedEditorOptions extends BaseEditorOptions {
    external_plugins: Record<string, string>;
    forced_plugins: string[];
    plugins: string[];
}
interface EditorOptions extends NormalizedEditorOptions {
    a11y_advanced_options: boolean;
    allow_unsafe_link_target: boolean;
    anchor_bottom: string;
    anchor_top: string;
    automatic_uploads: boolean;
    block_formats: string;
    body_class: string;
    body_id: string;
    br_newline_selector: string;
    color_map: string[];
    color_cols: number;
    color_cols_foreground: number;
    color_cols_background: number;
    color_default_background: string;
    color_default_foreground: string;
    content_css: string[];
    contextmenu: string[];
    custom_colors: boolean;
    document_base_url: string;
    draggable_modal: boolean;
    editable_class: string;
    font_css: string[];
    font_family_formats: string;
    font_size_classes: string;
    font_size_formats: string;
    font_size_legacy_values: string;
    font_size_style_values: string;
    forced_root_block: string;
    forced_root_block_attrs: Record<string, string>;
    format_noneditable_selector: string;
    height: number | string;
    iframe_attrs: Record<string, string>;
    images_file_types: string;
    images_upload_base_path: string;
    images_upload_credentials: boolean;
    images_upload_url: string;
    indent_use_margin: boolean;
    indentation: string;
    inline: boolean;
    inline_boundaries_selector: string;
    language: string;
    language_load: boolean;
    language_url: string;
    line_height_formats: string;
    menu: Record<string, {
        title: string;
        items: string;
    }>;
    menubar: boolean | string;
    model: string;
    no_newline_selector: string;
    noneditable_class: string;
    noneditable_regexp: RegExp[];
    object_resizing: string;
    paste_as_text: boolean;
    preview_styles: string;
    promotion: boolean;
    readonly: boolean;
    removed_menuitems: string;
    toolbar: boolean | string | string[] | Array<ToolbarGroup>;
    toolbar_groups: Record<string, GroupToolbarButtonSpec>;
    toolbar_location: ToolbarLocation;
    toolbar_mode: ToolbarMode;
    toolbar_persist: boolean;
    toolbar_sticky: boolean;
    toolbar_sticky_offset: number;
    text_patterns: Pattern[];
    text_patterns_lookup: DynamicPatternsLookup;
    visual: boolean;
    visual_anchor_class: string;
    visual_table_class: string;
    width: number | string;
}
declare type StyleMap = Record<string, string | number>;
interface StylesSettings {
    allow_script_urls?: boolean;
    allow_svg_data_urls?: boolean;
    url_converter?: URLConverter;
    url_converter_scope?: any;
}
interface Styles {
    parse: (css: string | undefined) => Record<string, string>;
    serialize: (styles: StyleMap, elementName?: string) => string;
}
declare type EventUtilsCallback<T> = (event: EventUtilsEvent<T>) => void | boolean;
declare type EventUtilsEvent<T> = NormalizedEvent<T> & {
    metaKey: boolean;
};
interface Callback$1<T> {
    func: EventUtilsCallback<T>;
    scope: any;
}
interface CallbackList<T> extends Array<Callback$1<T>> {
    fakeName: string | false;
    capture: boolean;
    nativeHandler: EventListener;
}
interface EventUtilsConstructor {
    readonly prototype: EventUtils;
    new (): EventUtils;
    Event: EventUtils;
}
declare class EventUtils {
    static Event: EventUtils;
    domLoaded: boolean;
    events: Record<number, Record<string, CallbackList<any>>>;
    private readonly expando;
    private hasFocusIn;
    private count;
    constructor();
    bind<K extends keyof HTMLElementEventMap>(target: any, name: K, callback: EventUtilsCallback<HTMLElementEventMap[K]>, scope?: any): EventUtilsCallback<HTMLElementEventMap[K]>;
    bind<T = any>(target: any, names: string, callback: EventUtilsCallback<T>, scope?: any): EventUtilsCallback<T>;
    unbind<K extends keyof HTMLElementEventMap>(target: any, name: K, callback?: EventUtilsCallback<HTMLElementEventMap[K]>): this;
    unbind<T = any>(target: any, names: string, callback?: EventUtilsCallback<T>): this;
    unbind(target: any): this;
    fire(target: any, name: string, args?: {}): this;
    dispatch(target: any, name: string, args?: {}): this;
    clean(target: any): this;
    destroy(): void;
    cancel<T>(e: EventUtilsEvent<T>): boolean;
    private executeHandlers;
}
interface SetAttribEvent {
    attrElm: HTMLElement;
    attrName: string;
    attrValue: string | boolean | number | null;
}
interface DOMUtilsSettings {
    schema: Schema;
    url_converter: URLConverter;
    url_converter_scope: any;
    ownEvents: boolean;
    keep_values: boolean;
    update_styles: boolean;
    root_element: HTMLElement | null;
    collect: boolean;
    onSetAttrib: (event: SetAttribEvent) => void;
    contentCssCors: boolean;
    referrerPolicy: ReferrerPolicy;
}
declare type Target = Node | Window;
declare type RunArguments<T extends Node = Node> = string | T | Array<string | T> | null;
declare type BoundEvent = [
    Target,
    string,
    EventUtilsCallback<any>,
    any
];
declare type Callback<K extends string> = EventUtilsCallback<MappedEvent<HTMLElementEventMap, K>>;
declare type RunResult<T, R> = T extends Array<any> ? R[] : false | R;
interface DOMUtils {
    doc: Document;
    settings: Partial<DOMUtilsSettings>;
    win: Window;
    files: Record<string, boolean>;
    stdMode: boolean;
    boxModel: boolean;
    styleSheetLoader: StyleSheetLoader;
    boundEvents: BoundEvent[];
    styles: Styles;
    schema: Schema;
    events: EventUtils;
    root: Node | null;
    isBlock: {
        (node: Node | null): node is HTMLElement;
        (node: string): boolean;
    };
    clone: (node: Node, deep: boolean) => Node;
    getRoot: () => HTMLElement;
    getViewPort: (argWin?: Window) => GeomRect;
    getRect: (elm: string | HTMLElement) => GeomRect;
    getSize: (elm: string | HTMLElement) => {
        w: number;
        h: number;
    };
    getParent: {
        <K extends keyof HTMLElementTagNameMap>(node: string | Node | null, selector: K, root?: Node): HTMLElementTagNameMap[K] | null;
        <T extends Element>(node: string | Node | null, selector: string | ((node: Node) => node is T), root?: Node): T | null;
        (node: string | Node | null, selector?: string | ((node: Node) => boolean | void), root?: Node): Node | null;
    };
    getParents: {
        <K extends keyof HTMLElementTagNameMap>(elm: string | HTMLElementTagNameMap[K] | null, selector: K, root?: Node, collect?: boolean): Array<HTMLElementTagNameMap[K]>;
        <T extends Element>(node: string | Node | null, selector: string | ((node: Node) => node is T), root?: Node, collect?: boolean): T[];
        (elm: string | Node | null, selector?: string | ((node: Node) => boolean | void), root?: Node, collect?: boolean): Node[];
    };
    get: {
        <T extends Node>(elm: T): T;
        (elm: string): HTMLElement | null;
    };
    getNext: (node: Node | null, selector: string | ((node: Node) => boolean)) => Node | null;
    getPrev: (node: Node | null, selector: string | ((node: Node) => boolean)) => Node | null;
    select: {
        <K extends keyof HTMLElementTagNameMap>(selector: K, scope?: string | Node): Array<HTMLElementTagNameMap[K]>;
        <T extends HTMLElement = HTMLElement>(selector: string, scope?: string | Node): T[];
    };
    is: {
        <T extends Element>(elm: Node | Node[] | null, selector: string): elm is T;
        (elm: Node | Node[] | null, selector: string): boolean;
    };
    add: (parentElm: RunArguments, name: string | Element, attrs?: Record<string, string | boolean | number | null>, html?: string | Node | null, create?: boolean) => HTMLElement;
    create: {
        <K extends keyof HTMLElementTagNameMap>(name: K, attrs?: Record<string, string | boolean | number | null>, html?: string | Node | null): HTMLElementTagNameMap[K];
        (name: string, attrs?: Record<string, string | boolean | number | null>, html?: string | Node | null): HTMLElement;
    };
    createHTML: (name: string, attrs?: Record<string, string | null>, html?: string) => string;
    createFragment: (html?: string) => DocumentFragment;
    remove: {
        <T extends Node>(node: T | T[], keepChildren?: boolean): typeof node extends Array<any> ? T[] : T;
        <T extends Node>(node: string, keepChildren?: boolean): T | false;
    };
    getStyle: {
        (elm: Element, name: string, computed: true): string;
        (elm: string | Element | null, name: string, computed?: boolean): string | undefined;
    };
    setStyle: (elm: string | Element | Element[], name: string, value: string | number | null) => void;
    setStyles: (elm: string | Element | Element[], stylesArg: StyleMap) => void;
    removeAllAttribs: (e: RunArguments<Element>) => void;
    setAttrib: (elm: RunArguments<Element>, name: string, value: string | boolean | number | null) => void;
    setAttribs: (elm: RunArguments<Element>, attrs: Record<string, string | boolean | number | null>) => void;
    getAttrib: (elm: string | Element | null, name: string, defaultVal?: string) => string;
    getAttribs: (elm: string | Element) => NamedNodeMap | Attr[];
    getPos: (elm: string | Element, rootElm?: Node) => {
        x: number;
        y: number;
    };
    parseStyle: (cssText: string) => Record<string, string>;
    serializeStyle: (stylesArg: StyleMap, name?: string) => string;
    addStyle: (cssText: string) => void;
    loadCSS: (url: string) => void;
    hasClass: (elm: string | Element, cls: string) => boolean;
    addClass: (elm: RunArguments<Element>, cls: string) => void;
    removeClass: (elm: RunArguments<Element>, cls: string) => void;
    toggleClass: (elm: RunArguments<Element>, cls: string, state?: boolean) => void;
    show: (elm: string | Node | Node[]) => void;
    hide: (elm: string | Node | Node[]) => void;
    isHidden: (elm: string | Node) => boolean;
    uniqueId: (prefix?: string) => string;
    setHTML: (elm: RunArguments<Element>, html: string) => void;
    getOuterHTML: (elm: string | Node) => string;
    setOuterHTML: (elm: string | Node | Node[], html: string) => void;
    decode: (text: string) => string;
    encode: (text: string) => string;
    insertAfter: {
        <T extends Node>(node: T | T[], reference: string | Node): T;
        <T extends Node>(node: RunArguments<T>, reference: string | Node): RunResult<typeof node, T>;
    };
    replace: {
        <T extends Node>(newElm: Node, oldElm: T | T[], keepChildren?: boolean): T;
        <T extends Node>(newElm: Node, oldElm: RunArguments<T>, keepChildren?: boolean): false | T;
    };
    rename: {
        <K extends keyof HTMLElementTagNameMap>(elm: Element, name: K): HTMLElementTagNameMap[K];
        (elm: Element, name: string): Element;
    };
    findCommonAncestor: (a: Node, b: Node) => Node | null;
    run<R, T extends Node>(this: DOMUtils, elm: T | T[], func: (node: T) => R, scope?: any): typeof elm extends Array<any> ? R[] : R;
    run<R, T extends Node>(this: DOMUtils, elm: RunArguments<T>, func: (node: T) => R, scope?: any): RunResult<typeof elm, R>;
    isEmpty: (node: Node, elements?: Record<string, any>) => boolean;
    createRng: () => Range;
    nodeIndex: (node: Node, normalized?: boolean) => number;
    split: {
        <T extends Node>(parentElm: Node, splitElm: Node, replacementElm: T): T | undefined;
        <T extends Node>(parentElm: Node, splitElm: T): T | undefined;
    };
    bind: {
        <K extends string>(target: Target, name: K, func: Callback<K>, scope?: any): Callback<K>;
        <K extends string>(target: Target[], name: K, func: Callback<K>, scope?: any): Callback<K>[];
    };
    unbind: {
        <K extends string>(target: Target, name?: K, func?: EventUtilsCallback<MappedEvent<HTMLElementEventMap, K>>): EventUtils;
        <K extends string>(target: Target[], name?: K, func?: EventUtilsCallback<MappedEvent<HTMLElementEventMap, K>>): EventUtils[];
    };
    fire: (target: Node | Window, name: string, evt?: {}) => EventUtils;
    dispatch: (target: Node | Window, name: string, evt?: {}) => EventUtils;
    getContentEditable: (node: Node) => string | null;
    getContentEditableParent: (node: Node) => string | null;
    destroy: () => void;
    isChildOf: (node: Node, parent: Node) => boolean;
    dumpRng: (r: Range) => string;
}
interface ClientRect {
    left: number;
    top: number;
    bottom: number;
    right: number;
    width: number;
    height: number;
}
interface BookmarkManager {
    getBookmark: (type?: number, normalized?: boolean) => Bookmark;
    moveToBookmark: (bookmark: Bookmark) => void;
}
interface ControlSelection {
    isResizable: (elm: Element) => boolean;
    showResizeRect: (elm: HTMLElement) => void;
    hideResizeRect: () => void;
    updateResizeRect: (evt: EditorEvent<any>) => void;
    destroy: () => void;
}
interface WriterSettings {
    element_format?: 'xhtml' | 'html';
    entities?: string;
    entity_encoding?: EntityEncoding;
    indent?: boolean;
    indent_after?: string;
    indent_before?: string;
}
declare type Attributes = Array<{
    name: string;
    value: string;
}>;
interface Writer {
    cdata: (text: string) => void;
    comment: (text: string) => void;
    doctype: (text: string) => void;
    end: (name: string) => void;
    getContent: () => string;
    pi: (name: string, text?: string) => void;
    reset: () => void;
    start: (name: string, attrs?: Attributes | null, empty?: boolean) => void;
    text: (text: string, raw?: boolean) => void;
}
interface HtmlSerializerSettings extends WriterSettings {
    inner?: boolean;
    validate?: boolean;
}
interface HtmlSerializer {
    serialize: (node: AstNode) => string;
}
interface DomSerializerSettings extends DomParserSettings, WriterSettings, SchemaSettings, HtmlSerializerSettings {
    url_converter?: URLConverter;
    url_converter_scope?: {};
}
interface DomSerializerImpl {
    schema: Schema;
    addNodeFilter: (name: string, callback: ParserFilterCallback) => void;
    addAttributeFilter: (name: string, callback: ParserFilterCallback) => void;
    getNodeFilters: () => ParserFilter[];
    getAttributeFilters: () => ParserFilter[];
    removeNodeFilter: (name: string, callback?: ParserFilterCallback) => void;
    removeAttributeFilter: (name: string, callback?: ParserFilterCallback) => void;
    serialize: {
        (node: Element, parserArgs: {
            format: 'tree';
        } & ParserArgs): AstNode;
        (node: Element, parserArgs?: ParserArgs): string;
    };
    addRules: (rules: string) => void;
    setRules: (rules: string) => void;
    addTempAttr: (name: string) => void;
    getTempAttrs: () => string[];
}
interface DomSerializer extends DomSerializerImpl {
}
interface EditorSelection {
    bookmarkManager: BookmarkManager;
    controlSelection: ControlSelection;
    dom: DOMUtils;
    win: Window;
    serializer: DomSerializer;
    editor: Editor;
    collapse: (toStart?: boolean) => void;
    setCursorLocation: {
        (node: Node, offset: number): void;
        (): void;
    };
    getContent: {
        (args: {
            format: 'tree';
        } & Partial<GetSelectionContentArgs>): AstNode;
        (args?: Partial<GetSelectionContentArgs>): string;
    };
    setContent: (content: string, args?: Partial<SetSelectionContentArgs>) => void;
    getBookmark: (type?: number, normalized?: boolean) => Bookmark;
    moveToBookmark: (bookmark: Bookmark) => void;
    select: (node: Node, content?: boolean) => Node;
    isCollapsed: () => boolean;
    isForward: () => boolean;
    setNode: (elm: Element) => Element;
    getNode: () => HTMLElement;
    getSel: () => Selection | null;
    setRng: (rng: Range, forward?: boolean) => void;
    getRng: () => Range;
    getStart: (real?: boolean) => Element;
    getEnd: (real?: boolean) => Element;
    getSelectedBlocks: (startElm?: Element, endElm?: Element) => Element[];
    normalize: () => Range;
    selectorChanged: (selector: string, callback: (active: boolean, args: {
        node: Node;
        selector: String;
        parents: Node[];
    }) => void) => EditorSelection;
    selectorChangedWithUnbind: (selector: string, callback: (active: boolean, args: {
        node: Node;
        selector: String;
        parents: Node[];
    }) => void) => {
        unbind: () => void;
    };
    getScrollContainer: () => HTMLElement | undefined;
    scrollIntoView: (elm?: HTMLElement, alignToTop?: boolean) => void;
    placeCaretAt: (clientX: number, clientY: number) => void;
    getBoundingClientRect: () => ClientRect | DOMRect;
    destroy: () => void;
    expand: (options?: {
        type: 'word';
    }) => void;
}
declare type EditorCommandCallback<S> = (this: S, ui: boolean, value: any) => void;
declare type EditorCommandsCallback = (command: string, ui: boolean, value?: any) => void;
interface Commands {
    state: Record<string, (command: string) => boolean>;
    exec: Record<string, EditorCommandsCallback>;
    value: Record<string, (command: string) => string>;
}
interface ExecCommandArgs {
    skip_focus?: boolean;
}
interface EditorCommandsConstructor {
    readonly prototype: EditorCommands;
    new (editor: Editor): EditorCommands;
}
declare class EditorCommands {
    private readonly editor;
    private commands;
    constructor(editor: Editor);
    execCommand(command: string, ui?: boolean, value?: any, args?: ExecCommandArgs): boolean;
    queryCommandState(command: string): boolean;
    queryCommandValue(command: string): string;
    addCommands<K extends keyof Commands>(commandList: Commands[K], type: K): void;
    addCommands(commandList: Record<string, EditorCommandsCallback>): void;
    addCommand<S>(command: string, callback: EditorCommandCallback<S>, scope: S): void;
    addCommand(command: string, callback: EditorCommandCallback<Editor>): void;
    queryCommandSupported(command: string): boolean;
    addQueryStateHandler<S>(command: string, callback: (this: S) => boolean, scope: S): void;
    addQueryStateHandler(command: string, callback: (this: Editor) => boolean): void;
    addQueryValueHandler<S>(command: string, callback: (this: S) => string, scope: S): void;
    addQueryValueHandler(command: string, callback: (this: Editor) => string): void;
}
interface RawString {
    raw: string;
}
declare type Primitive = string | number | boolean | Record<string | number, any> | Function;
declare type TokenisedString = [
    string,
    ...Primitive[]
];
declare type Untranslated = Primitive | TokenisedString | RawString | null | undefined;
declare type TranslatedString = string;
interface I18n {
    getData: () => Record<string, Record<string, string>>;
    setCode: (newCode: string) => void;
    getCode: () => string;
    add: (code: string, items: Record<string, string>) => void;
    translate: (text: Untranslated) => TranslatedString;
    isRtl: () => boolean;
    hasCode: (code: string) => boolean;
}
interface Observable<T extends {}> {
    fire<K extends string, U extends MappedEvent<T, K>>(name: K, args?: U, bubble?: boolean): EditorEvent<U>;
    dispatch<K extends string, U extends MappedEvent<T, K>>(name: K, args?: U, bubble?: boolean): EditorEvent<U>;
    on<K extends string>(name: K, callback: (event: EditorEvent<MappedEvent<T, K>>) => void, prepend?: boolean): EventDispatcher<T>;
    off<K extends string>(name?: K, callback?: (event: EditorEvent<MappedEvent<T, K>>) => void): EventDispatcher<T>;
    once<K extends string>(name: K, callback: (event: EditorEvent<MappedEvent<T, K>>) => void): EventDispatcher<T>;
    hasEventListeners(name: string): boolean;
}
interface URISettings {
    base_uri?: URI;
}
interface URIConstructor {
    readonly prototype: URI;
    new (url: string, settings?: URISettings): URI;
    getDocumentBaseUrl: (loc: {
        protocol: string;
        host?: string;
        href?: string;
        pathname?: string;
    }) => string;
    parseDataUri: (uri: string) => {
        type: string;
        data: string;
    };
}
interface SafeUriOptions {
    readonly allow_html_data_urls?: boolean;
    readonly allow_script_urls?: boolean;
    readonly allow_svg_data_urls?: boolean;
}
declare class URI {
    static parseDataUri(uri: string): {
        type: string | undefined;
        data: string;
    };
    static isDomSafe(uri: string, context?: string, options?: SafeUriOptions): boolean;
    static getDocumentBaseUrl(loc: {
        protocol: string;
        host?: string;
        href?: string;
        pathname?: string;
    }): string;
    source: string;
    protocol: string | undefined;
    authority: string | undefined;
    userInfo: string | undefined;
    user: string | undefined;
    password: string | undefined;
    host: string | undefined;
    port: string | undefined;
    relative: string | undefined;
    path: string;
    directory: string;
    file: string | undefined;
    query: string | undefined;
    anchor: string | undefined;
    settings: URISettings;
    constructor(url: string, settings?: URISettings);
    setPath(path: string): void;
    toRelative(uri: string): string;
    toAbsolute(uri: string, noHost?: boolean): string;
    isSameOrigin(uri: URI): boolean;
    toRelPath(base: string, path: string): string;
    toAbsPath(base: string, path: string): string;
    getURI(noProtoHost?: boolean): string;
}
interface EditorManager extends Observable<EditorManagerEventMap> {
    defaultOptions: RawEditorOptions;
    majorVersion: string;
    minorVersion: string;
    releaseDate: string;
    activeEditor: Editor | null;
    focusedEditor: Editor | null;
    baseURI: URI;
    baseURL: string;
    documentBaseURL: string;
    i18n: I18n;
    suffix: string;
    add(this: EditorManager, editor: Editor): Editor;
    addI18n: (code: string, item: Record<string, string>) => void;
    createEditor(this: EditorManager, id: string, options: RawEditorOptions): Editor;
    execCommand(this: EditorManager, cmd: string, ui: boolean, value: any): boolean;
    get(this: EditorManager): Editor[];
    get(this: EditorManager, id: number | string): Editor | null;
    init(this: EditorManager, options: RawEditorOptions): Promise<Editor[]>;
    overrideDefaults(this: EditorManager, defaultOptions: Partial<RawEditorOptions>): void;
    remove(this: EditorManager): void;
    remove(this: EditorManager, selector: string): void;
    remove(this: EditorManager, editor: Editor): Editor | null;
    setActive(this: EditorManager, editor: Editor): void;
    setup(this: EditorManager): void;
    translate: (text: Untranslated) => TranslatedString;
    triggerSave: () => void;
    _setBaseUrl(this: EditorManager, baseUrl: string): void;
}
interface EditorObservable extends Observable<EditorEventMap> {
    bindPendingEventDelegates(this: Editor): void;
    toggleNativeEvent(this: Editor, name: string, state: boolean): void;
    unbindAllNativeEvents(this: Editor): void;
}
interface ProcessorSuccess<T> {
    valid: true;
    value: T;
}
interface ProcessorError {
    valid: false;
    message: string;
}
declare type SimpleProcessor = (value: unknown) => boolean;
declare type Processor<T> = (value: unknown) => ProcessorSuccess<T> | ProcessorError;
interface BuiltInOptionTypeMap {
    'string': string;
    'number': number;
    'boolean': boolean;
    'array': any[];
    'function': Function;
    'object': any;
    'string[]': string[];
    'object[]': any[];
    'regexp': RegExp;
}
declare type BuiltInOptionType = keyof BuiltInOptionTypeMap;
interface BaseOptionSpec {
    immutable?: boolean;
    deprecated?: boolean;
    docsUrl?: string;
}
interface BuiltInOptionSpec<K extends BuiltInOptionType> extends BaseOptionSpec {
    processor: K;
    default?: BuiltInOptionTypeMap[K];
}
interface SimpleOptionSpec<T> extends BaseOptionSpec {
    processor: SimpleProcessor;
    default?: T;
}
interface OptionSpec<T, U> extends BaseOptionSpec {
    processor: Processor<U>;
    default?: T;
}
interface Options {
    register: {
        <K extends BuiltInOptionType>(name: string, spec: BuiltInOptionSpec<K>): void;
        <K extends keyof NormalizedEditorOptions>(name: K, spec: OptionSpec<NormalizedEditorOptions[K], EditorOptions[K]> | SimpleOptionSpec<NormalizedEditorOptions[K]>): void;
        <T, U>(name: string, spec: OptionSpec<T, U>): void;
        <T>(name: string, spec: SimpleOptionSpec<T>): void;
    };
    isRegistered: (name: string) => boolean;
    get: {
        <K extends keyof EditorOptions>(name: K): EditorOptions[K];
        <T>(name: string): T | undefined;
    };
    set: <K extends string, T>(name: K, value: K extends keyof NormalizedEditorOptions ? NormalizedEditorOptions[K] : T) => boolean;
    unset: (name: string) => boolean;
    isSet: (name: string) => boolean;
}
interface UploadResult$1 {
    element: HTMLImageElement;
    status: boolean;
    blobInfo: BlobInfo;
    uploadUri: string;
    removed: boolean;
}
interface EditorUpload {
    blobCache: BlobCache;
    addFilter: (filter: (img: HTMLImageElement) => boolean) => void;
    uploadImages: () => Promise<UploadResult$1[]>;
    uploadImagesAuto: () => Promise<UploadResult$1[]>;
    scanForImages: () => Promise<BlobInfoImagePair[]>;
    destroy: () => void;
}
declare type FormatChangeCallback = (state: boolean, data: {
    node: Node;
    format: string;
    parents: Element[];
}) => void;
interface FormatRegistry {
    get: {
        (name: string): Format[] | undefined;
        (): Record<string, Format[]>;
    };
    has: (name: string) => boolean;
    register: (name: string | Formats, format?: Format[] | Format) => void;
    unregister: (name: string) => Formats;
}
interface Formatter extends FormatRegistry {
    apply: (name: string, vars?: FormatVars, node?: Node | RangeLikeObject | null) => void;
    remove: (name: string, vars?: FormatVars, node?: Node | Range, similar?: boolean) => void;
    toggle: (name: string, vars?: FormatVars, node?: Node) => void;
    match: (name: string, vars?: FormatVars, node?: Node, similar?: boolean) => boolean;
    closest: (names: string[]) => string | null;
    matchAll: (names: string[], vars?: FormatVars) => string[];
    matchNode: (node: Node | null, name: string, vars?: FormatVars, similar?: boolean) => Format | undefined;
    canApply: (name: string) => boolean;
    formatChanged: (names: string, callback: FormatChangeCallback, similar?: boolean, vars?: FormatVars) => {
        unbind: () => void;
    };
    getCssText: (format: string | ApplyFormat) => string;
}
interface EditorMode {
    isReadOnly: () => boolean;
    set: (mode: string) => void;
    get: () => string;
    register: (mode: string, api: EditorModeApi) => void;
}
interface EditorModeApi {
    activate: () => void;
    deactivate: () => void;
    editorReadOnly: boolean;
}
interface Model {
    readonly table: {
        readonly getSelectedCells: () => HTMLTableCellElement[];
        readonly clearSelectedCells: (container: Node) => void;
    };
}
declare type ModelManager = AddOnManager<Model>;
interface Plugin {
    getMetadata?: () => {
        name: string;
        url: string;
    };
    init?: (editor: Editor, url: string) => void;
    [key: string]: any;
}
declare type PluginManager = AddOnManager<void | Plugin>;
interface ShortcutsConstructor {
    readonly prototype: Shortcuts;
    new (editor: Editor): Shortcuts;
}
declare type CommandFunc = string | [
    string,
    boolean,
    any
] | (() => void);
declare class Shortcuts {
    private readonly editor;
    private readonly shortcuts;
    private pendingPatterns;
    constructor(editor: Editor);
    add(pattern: string, desc: string | null, cmdFunc: CommandFunc, scope?: any): boolean;
    remove(pattern: string): boolean;
    private normalizeCommandFunc;
    private createShortcut;
    private hasModifier;
    private isFunctionKey;
    private matchShortcut;
    private executeShortcutAction;
}
interface RenderResult {
    iframeContainer?: HTMLElement;
    editorContainer: HTMLElement;
    api?: Partial<EditorUiApi>;
}
interface Theme {
    ui?: any;
    inline?: any;
    execCommand?: (command: string, ui?: boolean, value?: any) => boolean;
    destroy?: () => void;
    init?: (editor: Editor, url: string) => void;
    renderUI?: () => RenderResult;
    getNotificationManagerImpl?: () => NotificationManagerImpl;
    getWindowManagerImpl?: () => WindowManagerImpl;
}
declare type ThemeManager = AddOnManager<void | Theme>;
interface EditorConstructor {
    readonly prototype: Editor;
    new (id: string, options: RawEditorOptions, editorManager: EditorManager): Editor;
}
declare class Editor implements EditorObservable {
    documentBaseUrl: string;
    baseUri: URI;
    id: string;
    plugins: Record<string, Plugin>;
    documentBaseURI: URI;
    baseURI: URI;
    contentCSS: string[];
    contentStyles: string[];
    ui: EditorUi;
    mode: EditorMode;
    options: Options;
    shortcuts: Shortcuts;
    loadedCSS: Record<string, any>;
    editorCommands: EditorCommands;
    suffix: string;
    editorManager: EditorManager;
    hidden: boolean;
    inline: boolean;
    hasVisual: boolean;
    isNotDirty: boolean;
    annotator: Annotator;
    bodyElement: HTMLElement | undefined;
    bookmark: any;
    composing: boolean;
    container: HTMLElement;
    contentAreaContainer: HTMLElement;
    contentDocument: Document;
    contentWindow: Window;
    delegates: Record<string, EventUtilsCallback<any>> | undefined;
    destroyed: boolean;
    dom: DOMUtils;
    editorContainer: HTMLElement;
    editorUpload: EditorUpload;
    eventRoot: Element | undefined;
    formatter: Formatter;
    formElement: HTMLElement | undefined;
    formEventDelegate: ((e: Event) => void) | undefined;
    hasHiddenInput: boolean;
    iframeElement: HTMLIFrameElement | null;
    iframeHTML: string | undefined;
    initialized: boolean;
    notificationManager: NotificationManager;
    orgDisplay: string;
    orgVisibility: string | undefined;
    parser: DomParser;
    quirks: Quirks;
    readonly: boolean;
    removed: boolean;
    schema: Schema;
    selection: EditorSelection;
    serializer: DomSerializer;
    startContent: string;
    targetElm: HTMLElement;
    theme: Theme;
    model: Model;
    undoManager: UndoManager;
    windowManager: WindowManager;
    _beforeUnload: (() => void) | undefined;
    _eventDispatcher: EventDispatcher<NativeEventMap> | undefined;
    _nodeChangeDispatcher: NodeChange;
    _pendingNativeEvents: string[];
    _selectionOverrides: SelectionOverrides;
    _skinLoaded: boolean;
    bindPendingEventDelegates: EditorObservable['bindPendingEventDelegates'];
    toggleNativeEvent: EditorObservable['toggleNativeEvent'];
    unbindAllNativeEvents: EditorObservable['unbindAllNativeEvents'];
    fire: EditorObservable['fire'];
    dispatch: EditorObservable['dispatch'];
    on: EditorObservable['on'];
    off: EditorObservable['off'];
    once: EditorObservable['once'];
    hasEventListeners: EditorObservable['hasEventListeners'];
    constructor(id: string, options: RawEditorOptions, editorManager: EditorManager);
    render(): void;
    focus(skipFocus?: boolean): void;
    hasFocus(): boolean;
    translate(text: Untranslated): TranslatedString;
    getParam<K extends BuiltInOptionType>(name: string, defaultVal: BuiltInOptionTypeMap[K], type: K): BuiltInOptionTypeMap[K];
    getParam<K extends keyof NormalizedEditorOptions>(name: K, defaultVal?: NormalizedEditorOptions[K], type?: BuiltInOptionType): NormalizedEditorOptions[K];
    getParam<T>(name: string, defaultVal: T, type?: BuiltInOptionType): T;
    hasPlugin(name: string, loaded?: boolean): boolean;
    nodeChanged(args?: any): void;
    addCommand<S>(name: string, callback: EditorCommandCallback<S>, scope: S): void;
    addCommand(name: string, callback: EditorCommandCallback<Editor>): void;
    addQueryStateHandler<S>(name: string, callback: (this: S) => boolean, scope?: S): void;
    addQueryStateHandler(name: string, callback: (this: Editor) => boolean): void;
    addQueryValueHandler<S>(name: string, callback: (this: S) => string, scope: S): void;
    addQueryValueHandler(name: string, callback: (this: Editor) => string): void;
    addShortcut(pattern: string, desc: string, cmdFunc: string | [
        string,
        boolean,
        any
    ] | (() => void), scope?: any): void;
    execCommand(cmd: string, ui?: boolean, value?: any, args?: ExecCommandArgs): boolean;
    queryCommandState(cmd: string): boolean;
    queryCommandValue(cmd: string): string;
    queryCommandSupported(cmd: string): boolean;
    show(): void;
    hide(): void;
    isHidden(): boolean;
    setProgressState(state: boolean, time?: number): void;
    load(args?: Partial<SetContentArgs>): string;
    save(args?: Partial<GetContentArgs>): string;
    setContent(content: string, args?: Partial<SetContentArgs>): string;
    setContent(content: AstNode, args?: Partial<SetContentArgs>): AstNode;
    setContent(content: Content, args?: Partial<SetContentArgs>): Content;
    getContent(args: {
        format: 'tree';
    } & Partial<GetContentArgs>): AstNode;
    getContent(args?: Partial<GetContentArgs>): string;
    insertContent(content: string, args?: any): void;
    resetContent(initialContent?: string): void;
    isDirty(): boolean;
    setDirty(state: boolean): void;
    getContainer(): HTMLElement;
    getContentAreaContainer(): HTMLElement;
    getElement(): HTMLElement;
    getWin(): Window;
    getDoc(): Document;
    getBody(): HTMLElement;
    convertURL(url: string, name: string, elm?: string | Element): string;
    addVisual(elm?: HTMLElement): void;
    remove(): void;
    destroy(automatic?: boolean): void;
    uploadImages(): Promise<UploadResult$1[]>;
    _scanForImages(): Promise<BlobInfoImagePair[]>;
}
interface UrlObject {
    prefix: string;
    resource: string;
    suffix: string;
}
declare type WaitState = 'added' | 'loaded';
declare type AddOnConstructor<T> = (editor: Editor, url: string) => T;
interface AddOnManager<T> {
    items: AddOnConstructor<T>[];
    urls: Record<string, string>;
    lookup: Record<string, {
        instance: AddOnConstructor<T>;
    }>;
    get: (name: string) => AddOnConstructor<T> | undefined;
    requireLangPack: (name: string, languages?: string) => void;
    add: (id: string, addOn: AddOnConstructor<T>) => AddOnConstructor<T>;
    remove: (name: string) => void;
    createUrl: (baseUrl: UrlObject, dep: string | UrlObject) => UrlObject;
    load: (name: string, addOnUrl: string | UrlObject) => Promise<void>;
    waitFor: (name: string, state?: WaitState) => Promise<void>;
}
interface RangeUtils {
    walk: (rng: Range, callback: (nodes: Node[]) => void) => void;
    split: (rng: Range) => RangeLikeObject;
    normalize: (rng: Range) => boolean;
    expand: (rng: Range, options?: {
        type: 'word';
    }) => Range;
}
interface ScriptLoaderSettings {
    referrerPolicy?: ReferrerPolicy;
}
interface ScriptLoaderConstructor {
    readonly prototype: ScriptLoader;
    new (): ScriptLoader;
    ScriptLoader: ScriptLoader;
}
declare class ScriptLoader {
    static ScriptLoader: ScriptLoader;
    private settings;
    private states;
    private queue;
    private scriptLoadedCallbacks;
    private queueLoadedCallbacks;
    private loading;
    constructor(settings?: ScriptLoaderSettings);
    _setReferrerPolicy(referrerPolicy: ReferrerPolicy): void;
    loadScript(url: string): Promise<void>;
    isDone(url: string): boolean;
    markDone(url: string): void;
    add(url: string): Promise<void>;
    load(url: string): Promise<void>;
    remove(url: string): void;
    loadQueue(): Promise<void>;
    loadScripts(scripts: string[]): Promise<void>;
}
declare type TextProcessCallback = (node: Text, offset: number, text: string) => number;
interface Spot {
    container: Text;
    offset: number;
}
interface TextSeeker {
    backwards: (node: Node, offset: number, process: TextProcessCallback, root?: Node) => Spot | null;
    forwards: (node: Node, offset: number, process: TextProcessCallback, root?: Node) => Spot | null;
}
interface DomTreeWalkerConstructor {
    readonly prototype: DomTreeWalker;
    new (startNode: Node, rootNode: Node): DomTreeWalker;
}
declare class DomTreeWalker {
    private readonly rootNode;
    private node;
    constructor(startNode: Node, rootNode: Node);
    current(): Node | null | undefined;
    next(shallow?: boolean): Node | null | undefined;
    prev(shallow?: boolean): Node | null | undefined;
    prev2(shallow?: boolean): Node | null | undefined;
    private findSibling;
    private findPreviousNode;
}
interface Version {
    major: number;
    minor: number;
}
interface Env {
    transparentSrc: string;
    documentMode: number;
    cacheSuffix: any;
    container: any;
    canHaveCSP: boolean;
    windowsPhone: boolean;
    browser: {
        current: string | undefined;
        version: Version;
        isEdge: () => boolean;
        isChromium: () => boolean;
        isIE: () => boolean;
        isOpera: () => boolean;
        isFirefox: () => boolean;
        isSafari: () => boolean;
    };
    os: {
        current: string | undefined;
        version: Version;
        isWindows: () => boolean;
        isiOS: () => boolean;
        isAndroid: () => boolean;
        isMacOS: () => boolean;
        isLinux: () => boolean;
        isSolaris: () => boolean;
        isFreeBSD: () => boolean;
        isChromeOS: () => boolean;
    };
    deviceType: {
        isiPad: () => boolean;
        isiPhone: () => boolean;
        isTablet: () => boolean;
        isPhone: () => boolean;
        isTouch: () => boolean;
        isWebView: () => boolean;
        isDesktop: () => boolean;
    };
}
interface FakeClipboardItem {
    readonly items: Record<string, any>;
    readonly types: ReadonlyArray<string>;
    readonly getType: <D = any>(type: string) => D | undefined;
}
interface FakeClipboard {
    readonly FakeClipboardItem: (items: Record<string, any>) => FakeClipboardItem;
    readonly write: (data: FakeClipboardItem[]) => void;
    readonly read: () => FakeClipboardItem[] | undefined;
    readonly clear: () => void;
}
interface FocusManager {
    isEditorUIElement: (elm: Element) => boolean;
}
interface EntitiesMap {
    [name: string]: string;
}
interface Entities {
    encodeRaw: (text: string, attr?: boolean) => string;
    encodeAllRaw: (text: string) => string;
    encodeNumeric: (text: string, attr?: boolean) => string;
    encodeNamed: (text: string, attr?: boolean, entities?: EntitiesMap) => string;
    getEncodeFunc: (name: string, entities?: string) => (text: string, attr?: boolean) => string;
    decode: (text: string) => string;
}
interface IconPack {
    icons: Record<string, string>;
}
interface IconManager {
    add: (id: string, iconPack: IconPack) => void;
    get: (id: string) => IconPack;
    has: (id: string) => boolean;
}
interface Resource {
    load: <T = any>(id: string, url: string) => Promise<T>;
    add: (id: string, data: any) => void;
    unload: (id: string) => void;
}
type TextPatterns_d_Pattern = Pattern;
type TextPatterns_d_RawPattern = RawPattern;
type TextPatterns_d_DynamicPatternsLookup = DynamicPatternsLookup;
type TextPatterns_d_RawDynamicPatternsLookup = RawDynamicPatternsLookup;
type TextPatterns_d_DynamicPatternContext = DynamicPatternContext;
type TextPatterns_d_BlockCmdPattern = BlockCmdPattern;
type TextPatterns_d_BlockPattern = BlockPattern;
type TextPatterns_d_BlockFormatPattern = BlockFormatPattern;
type TextPatterns_d_InlineCmdPattern = InlineCmdPattern;
type TextPatterns_d_InlinePattern = InlinePattern;
type TextPatterns_d_InlineFormatPattern = InlineFormatPattern;
declare namespace TextPatterns_d {
    export { TextPatterns_d_Pattern as Pattern, TextPatterns_d_RawPattern as RawPattern, TextPatterns_d_DynamicPatternsLookup as DynamicPatternsLookup, TextPatterns_d_RawDynamicPatternsLookup as RawDynamicPatternsLookup, TextPatterns_d_DynamicPatternContext as DynamicPatternContext, TextPatterns_d_BlockCmdPattern as BlockCmdPattern, TextPatterns_d_BlockPattern as BlockPattern, TextPatterns_d_BlockFormatPattern as BlockFormatPattern, TextPatterns_d_InlineCmdPattern as InlineCmdPattern, TextPatterns_d_InlinePattern as InlinePattern, TextPatterns_d_InlineFormatPattern as InlineFormatPattern, };
}
interface Delay {
    setEditorInterval: (editor: Editor, callback: () => void, time?: number) => number;
    setEditorTimeout: (editor: Editor, callback: () => void, time?: number) => number;
}
declare type UploadResult = UploadResult$2;
interface ImageUploader {
    upload: (blobInfos: BlobInfo[], showNotification?: boolean) => Promise<UploadResult[]>;
}
declare type ArrayCallback$1<T, R> = (this: any, x: T, i: number, xs: ArrayLike<T>) => R;
declare type ObjCallback$1<T, R> = (this: any, value: T, key: string, obj: Record<string, T>) => R;
declare type ArrayCallback<T, R> = ArrayCallback$1<T, R>;
declare type ObjCallback<T, R> = ObjCallback$1<T, R>;
declare type WalkCallback<T> = (this: any, o: T, i: string, n: keyof T | undefined) => boolean | void;
interface Tools {
    is: (obj: any, type?: string) => boolean;
    isArray: <T>(arr: any) => arr is Array<T>;
    inArray: <T>(arr: ArrayLike<T>, value: T) => number;
    grep: {
        <T>(arr: ArrayLike<T> | null | undefined, pred?: ArrayCallback<T, boolean>): T[];
        <T>(arr: Record<string, T> | null | undefined, pred?: ObjCallback<T, boolean>): T[];
    };
    trim: (str: string | null | undefined) => string;
    toArray: <T>(obj: ArrayLike<T>) => T[];
    hasOwn: (obj: any, name: string) => boolean;
    makeMap: (items: ArrayLike<string> | string | undefined, delim?: string | RegExp, map?: Record<string, {}>) => Record<string, {}>;
    each: {
        <T>(arr: ArrayLike<T> | null | undefined, cb: ArrayCallback<T, void | boolean>, scope?: any): boolean;
        <T>(obj: Record<string, T> | null | undefined, cb: ObjCallback<T, void | boolean>, scope?: any): boolean;
    };
    map: {
        <T, R>(arr: ArrayLike<T> | null | undefined, cb: ArrayCallback<T, R>): R[];
        <T, R>(obj: Record<string, T> | null | undefined, cb: ObjCallback<T, R>): R[];
    };
    extend: (obj: Object, ext: Object, ...objs: Object[]) => any;
    walk: <T extends Record<string, any>>(obj: T, f: WalkCallback<T>, n?: keyof T, scope?: any) => void;
    resolve: (path: string, o?: Object) => any;
    explode: (s: string | string[], d?: string | RegExp) => string[];
    _addCacheSuffix: (url: string) => string;
}
interface KeyboardLikeEvent {
    shiftKey: boolean;
    ctrlKey: boolean;
    altKey: boolean;
    metaKey: boolean;
}
interface VK {
    BACKSPACE: number;
    DELETE: number;
    DOWN: number;
    ENTER: number;
    ESC: number;
    LEFT: number;
    RIGHT: number;
    SPACEBAR: number;
    TAB: number;
    UP: number;
    PAGE_UP: number;
    PAGE_DOWN: number;
    END: number;
    HOME: number;
    modifierPressed: (e: KeyboardLikeEvent) => boolean;
    metaKeyPressed: (e: KeyboardLikeEvent) => boolean;
}
interface DOMUtilsNamespace {
    (doc: Document, settings: Partial<DOMUtilsSettings>): DOMUtils;
    DOM: DOMUtils;
    nodeIndex: (node: Node, normalized?: boolean) => number;
}
interface RangeUtilsNamespace {
    (dom: DOMUtils): RangeUtils;
    compareRanges: (rng1: RangeLikeObject, rng2: RangeLikeObject) => boolean;
    getCaretRangeFromPoint: (clientX: number, clientY: number, doc: Document) => Range;
    getSelectedNode: (range: Range) => Node;
    getNode: (container: Node, offset: number) => Node;
}
interface AddOnManagerNamespace {
    <T>(): AddOnManager<T>;
    language: string | undefined;
    languageLoad: boolean;
    baseURL: string;
    PluginManager: PluginManager;
    ThemeManager: ThemeManager;
    ModelManager: ModelManager;
}
interface BookmarkManagerNamespace {
    (selection: EditorSelection): BookmarkManager;
    isBookmarkNode: (node: Node) => boolean;
}
interface TinyMCE extends EditorManager {
    geom: {
        Rect: Rect;
    };
    util: {
        Delay: Delay;
        Tools: Tools;
        VK: VK;
        URI: URIConstructor;
        EventDispatcher: EventDispatcherConstructor<any>;
        Observable: Observable<any>;
        I18n: I18n;
        LocalStorage: Storage;
        ImageUploader: ImageUploader;
    };
    dom: {
        EventUtils: EventUtilsConstructor;
        TreeWalker: DomTreeWalkerConstructor;
        TextSeeker: (dom: DOMUtils, isBlockBoundary?: (node: Node) => boolean) => TextSeeker;
        DOMUtils: DOMUtilsNamespace;
        ScriptLoader: ScriptLoaderConstructor;
        RangeUtils: RangeUtilsNamespace;
        Serializer: (settings: DomSerializerSettings, editor?: Editor) => DomSerializer;
        ControlSelection: (selection: EditorSelection, editor: Editor) => ControlSelection;
        BookmarkManager: BookmarkManagerNamespace;
        Selection: (dom: DOMUtils, win: Window, serializer: DomSerializer, editor: Editor) => EditorSelection;
        StyleSheetLoader: (documentOrShadowRoot: Document | ShadowRoot, settings: StyleSheetLoaderSettings) => StyleSheetLoader;
        Event: EventUtils;
    };
    html: {
        Styles: (settings?: StylesSettings, schema?: Schema) => Styles;
        Entities: Entities;
        Node: AstNodeConstructor;
        Schema: (settings?: SchemaSettings) => Schema;
        DomParser: (settings?: DomParserSettings, schema?: Schema) => DomParser;
        Writer: (settings?: WriterSettings) => Writer;
        Serializer: (settings?: HtmlSerializerSettings, schema?: Schema) => HtmlSerializer;
    };
    AddOnManager: AddOnManagerNamespace;
    Annotator: (editor: Editor) => Annotator;
    Editor: EditorConstructor;
    EditorCommands: EditorCommandsConstructor;
    EditorManager: EditorManager;
    EditorObservable: EditorObservable;
    Env: Env;
    FocusManager: FocusManager;
    Formatter: (editor: Editor) => Formatter;
    NotificationManager: (editor: Editor) => NotificationManager;
    Shortcuts: ShortcutsConstructor;
    UndoManager: (editor: Editor) => UndoManager;
    WindowManager: (editor: Editor) => WindowManager;
    DOM: DOMUtils;
    ScriptLoader: ScriptLoader;
    PluginManager: PluginManager;
    ThemeManager: ThemeManager;
    ModelManager: ModelManager;
    IconManager: IconManager;
    Resource: Resource;
    FakeClipboard: FakeClipboard;
    trim: Tools['trim'];
    isArray: Tools['isArray'];
    is: Tools['is'];
    toArray: Tools['toArray'];
    makeMap: Tools['makeMap'];
    each: Tools['each'];
    map: Tools['map'];
    grep: Tools['grep'];
    inArray: Tools['inArray'];
    extend: Tools['extend'];
    walk: Tools['walk'];
    resolve: Tools['resolve'];
    explode: Tools['explode'];
    _addCacheSuffix: Tools['_addCacheSuffix'];
}
declare const tinymce: TinyMCE;
export { AddOnManager, Annotator, AstNode, Bookmark, BookmarkManager, ControlSelection, DOMUtils, Delay, DomParser, DomParserSettings, DomSerializer, DomSerializerSettings, DomTreeWalker, Editor, EditorCommands, EditorEvent, EditorManager, EditorModeApi, EditorObservable, EditorOptions, EditorSelection, Entities, Env, EventDispatcher, EventUtils, EventTypes_d as Events, FakeClipboard, FocusManager, Format_d as Formats, Formatter, GeomRect, HtmlSerializer, HtmlSerializerSettings, I18n, IconManager, Model, ModelManager, NotificationApi, NotificationManager, NotificationSpec, Observable, Plugin, PluginManager, RangeUtils, RawEditorOptions, Rect, Resource, Schema, SchemaSettings, ScriptLoader, Shortcuts, StyleSheetLoader, Styles, TextPatterns_d as TextPatterns, TextSeeker, Theme, ThemeManager, TinyMCE, Tools, URI, Ui_d as Ui, UndoManager, VK, WindowManager, Writer, WriterSettings, tinymce as default };
