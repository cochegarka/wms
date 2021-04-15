После показа:

1. Удалить NGINX:
```bash
sudo systemctl stop nginx
sudo systemctl disable nginx
sudo dnf remove nginx
```

2. Остановить и удалить под с БД
2.5. Остановить и удалить Valet (?)
3. Удалить PHP и Composer:
```
sudo dnf remove composer
sudo dnf remove php-{cli,process,mbstring,mcrypt,xml}
sudo dnf autoremove
```
(также убрать драйвера php-pgsql)
4. Убрать из /etc/hosts маппинг localhost -> anton (voyager где-то глюканул)


https://developer.fedoraproject.org/start/sw/web-app/laravel5.html
