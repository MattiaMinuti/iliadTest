# Custom Domain Setup - iliadlocal & iliadApi

This guide explains how to set up custom domains `iliadlocal` and `iliadApi` for local development, providing a professional development experience with clean URLs for both frontend and backend.

## 🎯 Benefits

- **Clean URLs**: Access your app at `http://iliadlocal` instead of `http://localhost:3000`
- **Professional API**: Access your API at `http://iliadApi:8000` instead of `http://localhost:8000`
- **Professional Look**: No port numbers visible in the frontend address bar
- **Easy to Remember**: Custom domains that are easy to type and remember
- **Production-like**: Mimics production environment setup
- **Clear Separation**: Distinct domains for frontend and backend services

## 🔧 Setup Instructions

### Step 1: Add Domain to Hosts File

#### macOS/Linux
```bash
# Add both domains to hosts file
echo "127.0.0.1 iliadlocal iliadApi" | sudo tee -a /etc/hosts

# Verify they were added
grep -E "(iliadlocal|iliadApi)" /etc/hosts
```

#### Windows
1. Open **Command Prompt as Administrator**
2. Run the following command:
   ```cmd
   echo 127.0.0.1 iliadlocal iliadApi >> C:\Windows\System32\drivers\etc\hosts
   ```
3. Verify by opening the file in Notepad as Administrator:
   ```
   C:\Windows\System32\drivers\etc\hosts
   ```

### Step 2: Start the Application

```bash
# Start all services
docker-compose up -d

# Verify containers are running
docker-compose ps
```

### Step 3: Access the Application

- **Frontend**: http://iliadlocal (clean URL without port)
- **Backend API**: http://iliadApi:8000 (professional API domain)

## 🔍 Troubleshooting

### "This site can't be reached" Error

If you get a DNS error:

1. **Check hosts file**:
   ```bash
   grep iliadlocal /etc/hosts
   ```
   Should show: `127.0.0.1 iliadlocal`

2. **Clear DNS cache**:
   ```bash
   # macOS
   sudo dscacheutil -flushcache
   
   # Linux
   sudo systemctl restart systemd-resolved
   
   # Windows
   ipconfig /flushdns
   ```

3. **Restart browser** to clear DNS cache

### "Blocked request" Error

If you see "This host is not allowed":

1. **Check Vite configuration** in `fe/vite.config.js`:
   ```javascript
   server: {
     allowedHosts: ['iliadlocal', 'iliadLocal', 'localhost'],
     // ... other config
   }
   ```

2. **Restart frontend container**:
   ```bash
   docker-compose restart frontend
   ```

### Port Already in Use

If port 80 is already in use:

1. **Check what's using port 80**:
   ```bash
   # macOS/Linux
   sudo lsof -i :80
   
   # Windows
   netstat -ano | findstr :80
   ```

2. **Stop conflicting service** or change port in `docker-compose.yml`:
   ```yaml
   ports:
     - "8080:3000"  # Use port 8080 instead
   ```

## 🚀 Advanced Configuration

### Multiple Domains

You can add multiple domains to the hosts file:

```bash
# Add multiple domains
echo "127.0.0.1 iliadlocal iliad.local iliad-dev" | sudo tee -a /etc/hosts
```

Then update `vite.config.js`:
```javascript
allowedHosts: ['iliadlocal', 'iliad.local', 'iliad-dev', 'localhost']
```

### HTTPS Setup (Optional)

For HTTPS support, you can use a reverse proxy like nginx or traefik:

```yaml
# docker-compose.yml addition
services:
  nginx:
    image: nginx:alpine
    ports:
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
```

## 📝 Notes

- The hosts file change is **local to your machine only**
- Other developers need to add the same entry to their hosts file
- The domain `iliadlocal` is not accessible from other machines
- This setup is for **development only**, not production

## 🔄 Reverting Changes

To remove the custom domain:

1. **Remove from hosts file**:
   ```bash
   # macOS/Linux
   sudo sed -i '' '/iliadlocal/d' /etc/hosts
   
   # Windows - manually edit the file
   ```

2. **Use standard URLs**:
   - http://localhost (port 80)
   - http://localhost:3000 (if using different port)

## ✅ Verification

After setup, verify everything works:

1. **Check hosts file**:
   ```bash
   grep iliadlocal /etc/hosts
   ```

2. **Test DNS resolution**:
   ```bash
   ping iliadlocal
   # Should resolve to 127.0.0.1
   ```

3. **Access application**:
   - Open http://iliadlocal in browser
   - Should load the Gestionale Iliad application

4. **Check container logs**:
   ```bash
   docker-compose logs frontend
   # Should show Vite server running on port 80
   ```
