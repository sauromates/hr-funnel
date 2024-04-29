#syntax=docker/dockerfile:1.4

# Versions
FROM node:20-alpine AS node_upstream

# Base stage for dev and build
FROM node_upstream AS base

# Check https://github.com/nodejs/docker-node/tree/b4117f9333da4138b03a546ec926ef50a31506c3#nodealpine to understand why libc6-compat might be needed.
# hadolint ignore=DL3018
RUN apk add --no-cache libc6-compat

WORKDIR /srv/app

RUN corepack enable && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store

# Development image
FROM base as dev

EXPOSE 3000

CMD ["sh", "-c", "pnpm install; pnpm dev"]

FROM base AS builder

COPY --link pnpm-lock.yaml .npmrc ./
RUN pnpm fetch --prod

COPY --link . .

RUN	pnpm install --frozen-lockfile --offline --prod && \
	pnpm run build

# Production image, copy all the files and run compiled app
FROM node_upstream AS prod

WORKDIR /srv/app

ENV NODE_ENV production

RUN addgroup --system --gid 1001 nodejs; \
	adduser --system --uid 1001 nuxtjs

COPY --from=builder --link --chown=1001:1001 /srv/app/public ./public
COPY --from=builder --link --chown=1001:1001 /srv/app/.output ./.output

USER nuxtjs

EXPOSE 3000

ENV NUXT_PORT 3000
ENV NUXT_HOST "0.0.0.0"

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:3000 || exit 1

CMD ["node", ".output/server/index.mjs"]
