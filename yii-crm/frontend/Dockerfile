FROM node:18 AS build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM yiisoftware/yii2-php:8.2-apache
COPY --from=build /app/dist /var/www/html
RUN a2enmod rewrite
# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/frontend/web|g' /etc/apache2/sites-available/000-default.conf
#COPY ./apache.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 80
