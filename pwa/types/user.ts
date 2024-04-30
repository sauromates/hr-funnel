import type { Item } from "./item";

export interface User extends Item {
  id?: string;
  email: string;
  password?: string;
  name: string;
  roles: string[];
  createdAt?: Date;
  updatedAt?: Date;
}
