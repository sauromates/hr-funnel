export interface Item {
  '@context'?: `/contexts/${Capitalize<string>}`;
  '@id'?: string;
  '@type'?: string;
}
