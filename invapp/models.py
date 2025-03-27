import random
import string
from django.db import models
from django.contrib.auth.models import AbstractUser, Group, Permission
from django.utils import timezone

# To generate alphanumeric code
def generate_random_text(length=500):
    # Generate a random alphanumeric string of the given length.
    characters = string.ascii_letters + string.digits
    return ''.join(random.choices(characters, k=length))

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

    def __str__(self):
        return self.name
