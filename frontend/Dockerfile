# STEP 1 : build React
FROM node:18 AS build

ARG VITE_API_URL=http://localhost:8080
ARG VITE_TMDB_KEY=22eef7e96585baa751a8384b942e4470
ENV VITE_API_URL=$VITE_API_URL
ENV VITE_TMDB_KEY=$VITE_TMDB_KEY

WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
RUN npm run build

# STEP 2 : serveur Nginx
FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
