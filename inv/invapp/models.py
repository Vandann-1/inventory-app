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

def generate_category_code():
    try:
        latest_category = Categories.objects.order_by('-id').first()
        if latest_category and latest_category.custom_code:
            last_code = int(latest_category.custom_code.split('-')[-1])
            return f"CT-{last_code + 1:03d}"
    except OperationalError:
        # This prevents errors during migrations
        return "CT-001"
    return "CT-001"

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
    custom_code = models.CharField(max_length=10, unique=True, default=generate_category_code)  

    def save(self, *args, **kwargs):
        india_tz = pytz.timezone('Asia/Kolkata')
        self.created_at = timezone.now().astimezone(india_tz)
        super().save(*args, **kwargs)
    

class SubCategory(models.Model):
        category = models.ForeignKey(Categories, on_delete=models.CASCADE, related_name="subcategories")
        category_name = models.CharField(max_length=255)  
        category_code = models.CharField(max_length=500)  
        # subcategory_code = models.CharField(max_length=50, unique=True)
        description = models.TextField()
     
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