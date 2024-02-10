# Use an official Node.js image to build the Vue.js project
FROM node:latest as build-stage

# Set the working directory inside the container
WORKDIR /app

# Copy package.json and package-lock.json (if available)
COPY package*.json ./

# Install project dependencies
RUN npm install

# Copy the rest of the Vue.js project into the container
COPY . .

# Run Gulp task for styles using the npm script
RUN npm run styles

# Execute Gulp tasks, then build the Vue.js project
RUN npm run build

# Use an official Nginx image to serve the built project
FROM nginx:alpine as production-stage

# Copy the built project from the build-stage to the Nginx server
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Expose port 80 to the host for the Nginx server
EXPOSE 80

# Start Nginx when the container launches
CMD ["nginx", "-g", "daemon off;"]