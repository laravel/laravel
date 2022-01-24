# Sử dụng image nginx:1.19-alpine
FROM nginx:1.19-alpine 

# Copy toàn bộ nội dung ở trong default.conf vào file default.conf trong container
ADD ./.docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# Tạo thư mục /var/www/html
RUN mkdir -p /var/www/html
