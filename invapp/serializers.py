from rest_framework import serializers
from .models import CustomUser
from .models import Categories 


class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = CustomUser
        fields = ['id', 'email','password','username','mobile_no','role','is_active','user_code']

class CategorySerializer(serializers.ModelSerializer):
    class Meta:
        model = Categories
        fields = '__all__'
        

     