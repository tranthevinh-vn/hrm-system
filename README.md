# HRM System — DTC245240009

**Sinh vien:** Tran The Vinh  
**MSSV:** DTC245240009  
**Lop:** K23.K1.MMT K23A  
**Mon hoc:** Trien khai va Quan tri He thong Phan mem  
**De tai so:** 10 — He thong Quan ly Nhan su (HRM)

---

## Kien truc he thong

```
Client → Nginx:80/443 → hrm_web:80 → MySQL:3306
                      → phpMyAdmin:8081
Prometheus → node_exporter, cadvisor
Grafana → Prometheus, Loki
Promtail → Loki
```

## Huong dan chay

```bash
# 1. Clone
git clone https://github.com/DTC245240009/hrm-system.git
cd hrm-system

# 2. Tao .env
cp .env.example .env

# 3. Tao SSL
mkdir -p nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx/ssl/hrm.key -out nginx/ssl/hrm.crt \
  -subj "/C=VN/ST=ThaiNguyen/O=HRM/CN=localhost"

# 4. Khoi dong
docker compose up -d --build

# 5. Kiem tra
docker compose ps
```

## Dia chi truy cap

| Dich vu      | URL                        |
|-------------|---------------------------|
| HRM Web     | https://localhost           |
| phpMyAdmin  | http://localhost:8081       |
| Prometheus  | http://localhost:9090       |
| Grafana     | http://localhost:3000       |
