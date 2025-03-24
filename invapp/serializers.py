from rest_framework import serializers
from django.contrib.auth.models import User 
from .models import Categories 


class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = ['id', 'email','password','username','mobile_no']

from rest_framework_simplejwt.serializers import TokenObtainPairSerializer

class MyTokenObtainPairSerializer(TokenObtainPairSerializer):
    @classmethod
    def get_token(cls, user):
        token = super().get_token(user)
        token['email'] = user.email  # Add extra fields to the token
        return token


class CategorySerializer(serializers.ModelSerializer):
    class Meta:
        model = Categories
        fields = '__all__'
        

class CustomUserSerializer(serializers.ModelSerializer):  # for see user data in admin panel
    class Meta:
        model = User
        fields = ['id', 'username', 'email']               