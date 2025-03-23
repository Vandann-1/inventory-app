from django.db import models


class Categories(models.Model):
    name = models.CharField(max_length=255,unique=True)
    desc = models.TextField(null=True,blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

def __str__(self):
    return self.name