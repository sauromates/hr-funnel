import { z } from 'zod';
import type { Item } from '~/types/item';
import type { User } from '~/types/user';

export enum VacancyStatus {
  Draft = 'draft',
  Published = 'published',
}

export interface MaybeVacancy extends Item {
  id?: number;
  title: string;
  slug?: string;
  description?: string;
  shortDescription?: string;
  status?: VacancyStatus;
  manager?: User;
  minBudget: number;
  maxBudget?: number;
  requirements?: string[];
  createdBy?: User;
  createdAt?: Date;
  updatedAt?: Date;
}

export interface Vacancy extends MaybeVacancy {
  id: number;
  slug: string;
  createdBy: User;
  createdAt: Date;
  updatedAt: Date;
}

export const vacancyFormSchema = z
  .object({
    title: z.string().max(255),
    slug: z.string().optional(),
    description: z.string().optional(),
    shortDescription: z.string().max(255).optional(),
    status: z.nativeEnum(VacancyStatus).default(VacancyStatus.Draft),
    manager: z.string().url().optional(),
    minBudget: z.number(),
    maxBudget: z.number().optional(),
    requirements: z.string().array().optional(),
  })
  .refine((schema) => {
    if (schema.maxBudget !== undefined) {
      return schema.maxBudget >= schema.minBudget;
    }
  });

export type VacancyForm = z.infer<typeof vacancyFormSchema>;
export type VacancyFormErrorList = z.inferFormattedError<typeof vacancyFormSchema>;
