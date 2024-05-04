import type { Item } from './item';

export interface MaybeUser extends Item {
  id?: string;
  email: string;
  password?: string;
  name: string;
  roles: string[];
  createdAt?: Date;
  updatedAt?: Date;
}

export interface User extends MaybeUser {
  '@context': `/contexts/${Capitalize<string>}`;
  '@id': string;
  '@type': string;
  id: string;
  createdAt: Date;
  updatedAt: Date;
}

export type LoginCredentials = {
  email: string;
  password: string;
  remember?: boolean;
};
