from django.db import models
from django.contrib.auth.models import AbstractUser, Group, Permission
from django.utils import timezone

# for user registration using CustomUser
class CustomUser(AbstractUser):
    mobile_no = models.CharField(max_length=15, blank=True, null=True)
    groups = models.ManyToManyField(Group, related_name="invapp_user_groups", blank=True)  
    user_permissions = models.ManyToManyField(Permission, related_name="invapp_user_permissions", blank=True)

    def __str__(self):
        return self.username

# Categories model
class Categories(models.Model):
    name = models.CharField(max_length=255, unique=True)
    desc = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(default=timezone.now)  # Capture current time with timezone support

    def __str__(self):
        return self.name
