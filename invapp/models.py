from django.db import models


from django.contrib.auth.models import AbstractUser, Group, Permission
# for user registration
class User(AbstractUser):
    mobile_no = models.CharField(max_length=15, blank=True, null=True)
    groups = models.ManyToManyField(Group, related_name="invapp_user_groups", blank=True)  
    user_permissions = models.ManyToManyField(Permission, related_name="invapp_user_permissions", blank=True)


class Categories(models.Model):
    name = models.CharField(max_length=255,unique=True)
    desc = models.TextField(null=True,blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
    
    
    

def __str__(self):
    return self.name

