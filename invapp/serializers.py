from rest_framework import serializers
from django.contrib.auth.models import User 
from .models import Categories 


class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = ['id', 'email','password','username']


class CategorySerializer(serializers.ModelSerializer):
    class Meta:
        model = Categories
        fields = '__all__'
        

class CustomUserSerializer(serializers.ModelSerializer):  # for see user data in admin panel
    class Meta:
        model = User
        fields = ['id', 'username', 'email']               