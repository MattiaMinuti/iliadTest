// Vue i18n type declarations for IDE support
declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $t: (key: string, values?: Record<string, any>) => string;
    $tc: (key: string, choice?: number, values?: Record<string, any>) => string;
    $te: (key: string, locale?: string) => boolean;
    $d: (value: Date | number, key?: string, locale?: string) => string;
    $n: (value: number, key?: string, locale?: string) => string;
  }
}

// Global declarations for template usage
declare global {
  const $t: (key: string, values?: Record<string, any>) => string;
  const $tc: (
    key: string,
    choice?: number,
    values?: Record<string, any>
  ) => string;
  const $te: (key: string, locale?: string) => boolean;
  const $d: (value: Date | number, key?: string, locale?: string) => string;
  const $n: (value: number, key?: string, locale?: string) => string;
}

export {};
