from rest_framework import serializers
from .models import Categories , Product , CustomUser , Supplier , SubCategory
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
    

class SubCategorySerializer(serializers.ModelSerializer):
    category_name = serializers.CharField(source='category.name', read_only=True)
    category_code = serializers.CharField(source='category.category_code', read_only=True)

    class Meta:
        model = SubCategory
        fields = ['id', 'category', 'category_name', 'category_code', 'subcategory_code', 'description']
    

class ProductSerializer(serializers.ModelSerializer):
    image = serializers.ImageField(max_length=None, use_url=True)
    class Meta:
        model = Product
        fields = '__all__'
        
class SupplierSerializer(serializers.ModelSerializer):
    class Meta:
        model = Supplier
        fields = '__all__'        
        
        

     