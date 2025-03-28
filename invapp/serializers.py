from rest_framework import serializers
from .models import Categories , Product , CustomUser , Supplier
import pytz


class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = CustomUser
        fields = ['id', 'email','password','username','mobile_no','role','is_active','user_code']

class CategorySerializer(serializers.ModelSerializer):
    created_at = serializers.SerializerMethodField()

    class Meta:
        model = Categories
        fields = ['id', 'name', 'desc', 'created_at', 'custom_code', 'category_code']

    def get_created_at(self, obj):
        india_tz = pytz.timezone('Asia/Kolkata')
        return obj.created_at.astimezone(india_tz).strftime('%Y-%m-%d %H:%M:%S')

class ProductSerializer(serializers.ModelSerializer):
    image = serializers.ImageField(max_length=None, use_url=True)
    class Meta:
        model = Product
        fields = '__all__'
        
class SupplierSerializer(serializers.ModelSerializer):
    class Meta:
        model = Supplier
        fields = '__all__'        
        
        

     