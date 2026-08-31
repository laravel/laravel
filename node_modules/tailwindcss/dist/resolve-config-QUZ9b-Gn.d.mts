import _default from './colors.mjs';

type ArbitraryUtilityValue = {
    kind: 'arbitrary';
    /**
     * ```
     * bg-[color:var(--my-color)]
     *     ^^^^^
     *
     * bg-(color:--my-color)
     *     ^^^^^
     * ```
     */
    dataType: string | null;
    /**
     * ```
     * bg-[#0088cc]
     *     ^^^^^^^
     *
     * bg-[var(--my_variable)]
     *     ^^^^^^^^^^^^^^^^^^
     *
     * bg-(--my_variable)
     *     ^^^^^^^^^^^^^^
     * ```
     */
    value: string;
};
type NamedUtilityValue = {
    kind: 'named';
    /**
     * ```
     * bg-red-500
     *    ^^^^^^^
     *
     * w-1/2
     *   ^
     * ```
     */
    value: string;
    /**
     * ```
     * w-1/2
     *   ^^^
     * ```
     */
    fraction: string | null;
};
type ArbitraryModifier = {
    kind: 'arbitrary';
    /**
     * ```
     * bg-red-500/[50%]
     *             ^^^
     * ```
     */
    value: string;
};
type NamedModifier = {
    kind: 'named';
    /**
     * ```
     * bg-red-500/50
     *            ^^
     * ```
     */
    value: string;
};
type ArbitraryVariantValue = {
    kind: 'arbitrary';
    value: string;
};
type NamedVariantValue = {
    kind: 'named';
    value: string;
};
type Variant = 
/**
 * Arbitrary variants are variants that take a selector and generate a variant
 * on the fly.
 *
 * E.g.: `[&_p]`
 */
{
    kind: 'arbitrary';
    selector: string;
    relative: boolean;
}
/**
 * Static variants are variants that don't take any arguments.
 *
 * E.g.: `hover`
 */
 | {
    kind: 'static';
    root: string;
}
/**
 * Functional variants are variants that can take an argument. The argument is
 * either a named variant value or an arbitrary variant value.
 *
 * E.g.:
 *
 * - `aria-disabled`
 * - `aria-[disabled]`
 * - `@container-size`          -> @container, with named value `size`
 * - `@container-[inline-size]` -> @container, with arbitrary variant value `inline-size`
 * - `@container`               -> @container, with no value
 */
 | {
    kind: 'functional';
    root: string;
    value: ArbitraryVariantValue | NamedVariantValue | null;
    modifier: ArbitraryModifier | NamedModifier | null;
}
/**
 * Compound variants are variants that take another variant as an argument.
 *
 * E.g.:
 *
 * - `has-[&_p]`
 * - `group-*`
 * - `peer-*`
 */
 | {
    kind: 'compound';
    root: string;
    modifier: ArbitraryModifier | NamedModifier | null;
    variant: Variant;
};
type Candidate = 
/**
 * Arbitrary candidates are candidates that register utilities on the fly with
 * a property and a value.
 *
 * E.g.:
 *
 * - `[color:red]`
 * - `[color:red]/50`
 * - `[color:red]/50!`
 */
{
    kind: 'arbitrary';
    property: string;
    value: string;
    modifier: ArbitraryModifier | NamedModifier | null;
    variants: Variant[];
    important: boolean;
    raw: string;
}
/**
 * Static candidates are candidates that don't take any arguments.
 *
 * E.g.:
 *
 * - `underline`
 * - `box-border`
 */
 | {
    kind: 'static';
    root: string;
    variants: Variant[];
    important: boolean;
    raw: string;
}
/**
 * Functional candidates are candidates that can take an argument.
 *
 * E.g.:
 *
 * - `bg-red-500`
 * - `bg-[#0088cc]`
 * - `w-1/2`
 */
 | {
    kind: 'functional';
    root: string;
    value: ArbitraryUtilityValue | NamedUtilityValue | null;
    modifier: ArbitraryModifier | NamedModifier | null;
    variants: Variant[];
    important: boolean;
    raw: string;
};

type PluginUtils = {
    theme: (keypath: string, defaultValue?: any) => any;
    colors: typeof _default;
};

export type { Candidate as C, NamedUtilityValue as N, PluginUtils as P, Variant as V };
