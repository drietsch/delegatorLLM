# Build stage
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Production stage - serve static files
FROM pierrezemb/gostatic
COPY --from=build /app/dist /srv/http/
CMD ["-port", "8080", "-https-promote", "-enable-logging"]
