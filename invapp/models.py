import random
import string
from django.db import models
from django.contrib.auth.models import AbstractUser, Group, Permission
from django.utils import timezone
from django.db.utils import OperationalError
import pytz


# To generate alphanumeric code
def generate_random_text(length=500):
    # Generate a random alphanumeric string of the given length.
    characters = string.ascii_letters + string.digits
    return ''.join(random.choices(characters, k=length))

# Dynamic function to generate category codes for many models
def generate_custom_code(model_class, prefix):
    try:
        latest_instance = model_class.objects.order_by('-id').first()
        if model_class == Categories:
            field_name = 'category_code'
        elif model_class == SubCategory:
            field_name = 'subcategory_custom_code'
        else:
            field_name = 'custom_code'
        
        # Get the last code based on the correct field name
        if latest_instance and getattr(latest_instance, field_name, None):
            last_code = int(getattr(latest_instance, field_name).split('-')[-1])
            return f"{prefix}-{last_code + 1:03d}"
    except OperationalError:
        return f"{prefix}-001"
    return f"{prefix}-001"

def get_category_custom_code():
    return generate_custom_code(Categories, 'CT')

def get_subcategory_custom_code():
    return generate_custom_code(SubCategory, 'SCT')


# for user registration using CustomUser
class CustomUser(AbstractUser):
    mobile_no = models.CharField(max_length=15, blank=True, null=True)
    groups = models.ManyToManyField(Group, related_name="invapp_user_groups", blank=True)  
    user_permissions = models.ManyToManyField(Permission, related_name="invapp_user_permissions", blank=True)
    user_code = models.CharField(max_length=500, default=generate_random_text, unique=True)  # 500-character text
    role = models.CharField(max_length=255, default='user')

    def __str__(self):
        return self.username


# Categories model
class Categories(models.Model):
    name = models.CharField(max_length=255, unique=True)
    desc = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(default=timezone.now)  # Capture current time with timezone support
    category_code = models.CharField(max_length=500, default=generate_random_text, unique=True)  # 500-character text
    custom_code = models.CharField(max_length=10, unique=True, default=get_category_custom_code)  # For 'CT001' format
    status = models.BooleanField(default=True)  # Active/Inactive status

    def save(self, *args, **kwargs):
        india_tz = pytz.timezone('Asia/Kolkata')
        self.created_at = timezone.now().astimezone(india_tz)
        super().save(*args, **kwargs)
    

class SubCategory(models.Model):
        category = models.ForeignKey(Categories, on_delete=models.CASCADE, related_name="subcategories") # Foreign key to Categories model i.e parent category
        category_name = models.CharField(max_length=255, default='Default Category Name')
        category_code = models.CharField(max_length=500, default='DEFAULT_CODE')
        subcategory_name = models.CharField(max_length=255, unique=True, default="Default Subcategory") # Subcategory name
        subcategory_custom_code = models.CharField(max_length=50, unique=True, default=get_subcategory_custom_code) # For 'SCT001' format
        created_at = models.DateTimeField(default=timezone.now)  # Capture current time with timezone support
        desc = models.TextField()
        subcategory_code = models.CharField(max_length=500, default=generate_random_text, unique=True)  # 500-character text
        status = models.BooleanField(default=True)  # Active/Inactive status
        
     
        def save(self, *args, **kwargs):
            """Automatically sync category name and category code from the related Category model."""
            if self.category:
                self.category_name = self.category.name
                self.category_code = self.category.category_code
            super().save(*args, **kwargs)

        def __str__(self):
             return f" {self.description}"
    
#  product  model

class Product(models.Model):
    name = models.CharField(max_length=255, unique=True)
    desc = models.TextField(null=True, blank=True)
    purs_price = models.DecimalField(max_digits=10, decimal_places=2)
    selling_price = models.DecimalField(max_digits=10, decimal_places=2)
    created_at = models.DateTimeField(default=timezone.now)  
    image= models.ImageField(upload_to='product_image', null=True, blank=True)
    supplier = models.ForeignKey('Supplier', on_delete=models.CASCADE, related_name='products',default=1)    
    
    def __str__(self):
        return self.name

class Supplier(models.Model):
    name = models.CharField(max_length=255, unique=True)
    contact= models.CharField(max_length=20)
    email = models.EmailField(max_length=255, unique=True)
    
    def __str__(self):
        return self.name