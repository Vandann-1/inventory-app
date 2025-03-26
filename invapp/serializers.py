from rest_framework import serializers
from .models import CustomUser
from .models import Categories 


class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = CustomUser
        fields = ['id', 'email','password','username','mobile_no','is_active']
        


class CategorySerializer(serializers.ModelSerializer):
    class Meta:
        model = Categories
        fields = '__all__'
        

""" class CustomUserSerializer(serializers.ModelSerializer):  # for see user data in admin panel
    class Meta:
        model = User
        fields = ['id', 'username', 'email']      """          