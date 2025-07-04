# Admin-Driven Website CMS

This project has been transformed into a fully admin-driven content management system. All website content can now be controlled from the admin panel.

## 🚀 Quick Setup

1. **Run the setup script**: Visit `http://localhost/project1/setup.php`
2. **Login to admin**: Use your existing admin credentials
3. **Access CMS**: Go to the "Website CMS" button in your admin dashboard

## 📋 Features

### ✅ **Fully Admin Controlled**
- **Company Information**: Name, tagline, about content, contact details
- **Hero Slides**: Homepage slider with custom content and backgrounds
- **Services**: Add/edit/remove company services with icons and features
- **Statistics**: Company achievement numbers and icons
- **Social Media**: Facebook, Twitter, Instagram, LinkedIn links

### 🎨 **Modern Design**
- Responsive design works on all devices
- Beautiful animations and hover effects
- Professional color scheme
- FontAwesome icons throughout

### 🛠️ **Admin Features**
- Easy-to-use CMS dashboard
- Real-time preview of changes
- Drag-and-drop management
- Status controls (active/inactive)
- Display order management

## 📁 CMS Pages

- **CMS Dashboard** (`cms/cms_dashboard.php`) - Overview and quick actions
- **Company Settings** (`cms/company_settings.php`) - Company info management  
- **Hero Slides** (`cms/hero_slides.php`) - Homepage slider management
- **Services Management** (`cms/services_management.php`) - Services content
- **Company Statistics** (`cms/company_stats.php`) - Achievement numbers

## 🗄️ Database Tables

The following tables were created for the CMS:

- `company_info` - Company details and contact information
- `hero_slides` - Homepage slider content
- `services` - Company services with features
- `company_stats` - Statistics/achievements display

## 🔧 How to Use

### 1. Company Information
- Update company name, tagline, and about content
- Manage contact details (phone, email, address)
- Set working hours
- Add social media links

### 2. Hero Slides
- Create multiple slides for homepage carousel
- Choose from predefined gradient backgrounds
- Set custom titles, subtitles, and button text
- Control slide order and active status

### 3. Services
- Add unlimited services with descriptions
- Choose icons from FontAwesome library
- Add feature lists for each service
- Manage display order and status

### 4. Statistics
- Add achievement numbers (clients, projects, etc.)
- Choose appropriate icons
- Control what displays in the about section

## 🌐 Website Access

- **Public Website**: `http://localhost/project1/`
- **Admin Login**: `http://localhost/project1/auth/login.php`
- **CMS Dashboard**: `http://localhost/project1/cms/cms_dashboard.php`

## 📱 Features Included

- ✅ Responsive design for all devices
- ✅ SEO-friendly structure
- ✅ Fast loading with optimized assets
- ✅ Professional animations
- ✅ Contact form (frontend only)
- ✅ Social media integration
- ✅ Admin access floating button
- ✅ Cross-browser compatibility

## 🔒 Security

- All admin pages require authentication
- Database queries use prepared statements
- Input sanitization and validation
- XSS protection with htmlspecialchars()

## 📝 Customization

The system is designed to be easily customizable:

- Add new content types by creating database tables
- Extend the CMS with additional management pages
- Customize the website design in `index.php`
- Add new admin features as needed

## 🆘 Support

If you encounter any issues:

1. Check that your database connection is working
2. Ensure all SQL tables were created successfully
3. Verify admin login credentials are working
4. Check file permissions for uploads directory

## 🎯 Next Steps

1. **Customize your content** through the CMS interface
2. **Add your company logo** to the uploads directory
3. **Test the website** on different devices
4. **Set up a contact form handler** if needed
5. **Configure social media links**

---

**Note**: Remember to delete `setup.php` after initial setup for security reasons.
