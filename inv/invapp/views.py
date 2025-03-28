from rest_framework.decorators import api_view ,parser_classes
from rest_framework.parsers import MultiPartParser, FormParser
from rest_framework.response import Response
from django.contrib.auth import get_user_model, authenticate
from rest_framework_simplejwt.tokens import RefreshToken
from rest_framework import status
from django.core.files.storage import default_storage

# serializer================================
from .serializers import UserSerializer, CategorySerializer , ProductSerializer , SupplierSerializer , SubCategorySerializer
#=============================================

from .models import Categories, CustomUser , Product , Supplier
from django.utils import timezone
import pytz

# For preventing repeated failed login
import logging
logger = logging.getLogger(__name__)

User = get_user_model()

# User Login with failed login attempts protection
from django.core.cache import cache
# Constants
MAX_ATTEMPTS = 2
LOCKOUT_TIME = 10  # in minutes

@api_view(['POST'])
def login(request):
    try:
        email = request.data.get('email')
        password = request.data.get('password')

        if not email or not password:
            return Response({"message": "Email and password are required"}, status=status.HTTP_400_BAD_REQUEST)

        # Check if the account is locked
        lockout_key = f"lockout_{email}"
        failed_attempts_key = f"failed_attempts_{email}"

        if cache.get(lockout_key):
            return Response({"message": f"Too many failed attempts. Please try again after {LOCKOUT_TIME} minutes."}, status=status.HTTP_403_FORBIDDEN)

        user = authenticate(request, username=email, password=password)

        if user is not None:
            # Check if the user is active
            if not user.is_active:
                return Response({"message": "Access to this account is restricted. Contact Admin"}, status=status.HTTP_403_FORBIDDEN)
            
            # Clear failed attempts on successful login
            cache.delete(failed_attempts_key)
            cache.delete(lockout_key)

            refresh = RefreshToken.for_user(user)
            user_data = UserSerializer(user).data
            return Response({
                "message": "Login successful",
                "token": str(refresh.access_token),
                "refresh": str(refresh),
                "user": user_data
            }, status=status.HTTP_200_OK)
        else:
            # Track failed attempts
            failed_attempts = cache.get(failed_attempts_key, 0) + 1
            cache.set(failed_attempts_key, failed_attempts, timeout=LOCKOUT_TIME * 60)

            if failed_attempts >= MAX_ATTEMPTS:
                cache.set(lockout_key, True, timeout=LOCKOUT_TIME * 60)
                return Response({"message": f"Account locked due to too many failed attempts. Try again after {LOCKOUT_TIME} minutes."}, status=status.HTTP_403_FORBIDDEN)

            return Response({"message": "Invalid credentials"}, status=status.HTTP_401_UNAUTHORIZED)
    except Exception as e:
        return Response({"message": "Something went wrong", "error": str(e)}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


@api_view(['POST'])
def register(request):
    user_code = request.data.get('user_code')
    try:
        password = request.data.get('password')
        email = request.data.get('email')
        role = request.data.get('role')
        full_name = request.data.get('full_name')
        mobile_no = request.data.get('mobile_no')

        if not password or not email or not full_name:
            return Response({"message": "All fields are required"}, status=status.HTTP_400_BAD_REQUEST)

        # Check if user_code is provided for update
        if user_code:
            try:
                user = User.objects.get(user_code=user_code)
                
                # Check for email or mobile number conflicts (excluding current user)
                if User.objects.filter(email=email).exclude(user_code=user_code).exists():
                    return Response({"message": "Email already exists for another user"}, status=status.HTTP_409_CONFLICT)
                
                if User.objects.filter(mobile_no=mobile_no).exclude(user_code=user_code).exists():
                    return Response({"message": "Mobile number already exists for another user"}, status=status.HTTP_409_CONFLICT)

                # Update user data
                name_parts = full_name.strip().split(' ', 1)
                user.first_name = name_parts[0]
                user.last_name = name_parts[1] if len(name_parts) > 1 else ''
                user.username = full_name
                user.email = email
                user.role = role
                user.mobile_no = mobile_no
                
                # Update password if provided
                if password:
                    user.set_password(password)
                
                user.save()
                user_data = UserSerializer(user).data
                refresh = RefreshToken.for_user(request.user) if request.user.is_authenticated else RefreshToken()
                return Response({
                    "message": "User updated successfully!",
                    "token": str(refresh.access_token),
                    "refresh": str(refresh), 
                    "user": user_data}, status=status.HTTP_200_OK)
            
            except User.DoesNotExist:
                return Response({"message": "User not found"}, status=status.HTTP_404_NOT_FOUND)

        # Check for duplicate email or mobile during new user creation
        if User.objects.filter(email=email).exists():
            return Response({"message": "User with the same email already exists"}, status=status.HTTP_409_CONFLICT)
        
        if User.objects.filter(mobile_no=mobile_no).exists():
            return Response({"message": "User with the same mobile no already exists"}, status=status.HTTP_409_CONFLICT)

        # Create new user
        name_parts = full_name.strip().split(' ', 1)
        first_name = name_parts[0]
        last_name = name_parts[1] if len(name_parts) > 1 else ''
        full_name = first_name + ' ' + last_name

        user = User.objects.create_user(
            username=full_name, 
            password=password, 
            email=email,
            role=role,
            first_name=first_name,
            last_name=last_name,
            mobile_no=mobile_no
        )
        
        refresh = RefreshToken.for_user(request.user) if request.user.is_authenticated else RefreshToken()
        user_data = UserSerializer(user).data

        return Response({
            "message": "User Created Successfully!",
            "token": str(refresh.access_token),
            "refresh": str(refresh),
            "user": user_data
        }, status=status.HTTP_201_CREATED)

    except Exception as e:
        return Response({"message": "User registration failed", "error": str(e)}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)


# logic for add categories {add_category}

@api_view(['GET'])
def users(request):
    # If user_code is provided, fetch particular user
    user_code = request.GET.get('user_code')

    if user_code:
        try:
            user = CustomUser.objects.get(user_code=user_code)
            user_data = UserSerializer(user).data
            return Response({
                "id": user_data["id"],
                "username": user_data["username"],
                "email": user_data["email"],
                "role": user_data["role"],
                "mobile_no": user_data["mobile_no"],
                "status": "1" if user_data["is_active"] else "0",  # if is_active = true then 1 else 0
                "url_code": user_data["user_code"]
            })
        except CustomUser.DoesNotExist:
            return Response({"message": "User not found"}, status=404)

    # Fetch all users and serialize
    users = CustomUser.objects.all()
    serializer = UserSerializer(users, many=True)

    # Loop through serialized data (if you want to format it manually)
    data = []
    for user in serializer.data:
        data.append({
            "id": user["id"],
            "username": user["username"],
            "email": user["email"],
            "role": user["role"],
            "mobile_no": user["mobile_no"],
            "status": "1" if user["is_active"] else "0",  # if is_active = true then 1 else 0
            "url_code": user["user_code"]
        })

    return Response({"users": data})  # Return formatted data

@api_view(['POST'])
def bulk_delete(request):
    type = request.data.get('type')
    if type == 'categories':
        category_codes = request.data.get('category_codes', [])
        if not category_codes:
            return Response({"message": "No categories selected for deletion."}, status=400)

        deleted_count, _ = Categories.objects.filter(category_code__in=category_codes).delete()

        if deleted_count == 0:
            return Response({"message": "No matching categories found to delete."}, status=404)

        return Response({"success": True, "message": "Selected categories deleted successfully."})
        
    elif type == 'users':
        user_codes = request.data.get('user_codes', [])
        if not user_codes:
            return Response({"message": "No users selected for deletion."}, status=400)

        deleted_count, _ = CustomUser.objects.filter(user_code__in=user_codes).delete()

        if deleted_count == 0:
            return Response({"message": "No matching users found to delete."}, status=404)

        return Response({"success": True, "message": "Selected users deleted successfully."})



@api_view(['POST'])
def user_management(request):
    action = request.data.get('action')
    user_id = request.data.get('user_id')

    if not action or not user_id:
        return Response({"message": "Missing action or user_id"}, status=400)

    if action not in {'active', 'inactive'}:
        return Response({"message": "Invalid action"}, status=400)

    try:
        user = CustomUser.objects.get(user_code=user_id)

        user.is_active = True if action == 'active' else False
        user.save()

        status_message = "activated" if user.is_active else "deactivated"
        return Response({"message": f"User {status_message} successfully"}, status=200)

    except CustomUser.DoesNotExist:
        return Response({"message": "User not found"}, status=404)

    except Exception as e:
        return Response({"message": f"Error: {str(e)}"}, status=500)
    
  
    
# add categories for  
   

""" @api_view(['GET', 'POST'])
def categories(request):
    if request.method == 'POST':
        category_name = request.data.get('category_name')
        category_desc = request.data.get('category_desc')

        # Validation
        if not category_name or category_desc is None:
            return Response({"message": "All fields are required"}, status=400)

        if Categories.objects.filter(name=category_name).exists():
            return Response({"message": "Category with the same name already exists"}, status=409)

        # Create Category
        category = Categories.objects.create(
            name=category_name,
            desc=category_desc
        )
        serializer = CategorySerializer(category)

        refresh = RefreshToken.for_user(request.user) if request.user.is_authenticated else RefreshToken()

        return Response({
            "message": "Category Created Successfully!",
            "token": str(refresh.access_token),
            "refresh": str(refresh),
            "data": serializer.data
        }, status=201)

    # Handle GET Requests
    category_code = request.GET.get('category_code')

    if category_code:
        try:
            category = Categories.objects.get(category_code=category_code)
            serializer = CategorySerializer(category)
            return Response(serializer.data)
        except Categories.DoesNotExist:
            return Response({"message": "Category not found"}, status=404)

    # Retrieve All Categories
    categories = Categories.objects.all()
    serializer = CategorySerializer(categories, many=True)
    return Response({"Categories": serializer.data})

# To show the list of all product and if product_code is provided, fetch particular user
 """




@api_view(['GET', 'POST'])
def categories(request):
    category_code = request.data.get('category_code')
    if request.method == 'POST':
        try:
            category_name = request.data.get('category_name')
            category_desc = request.data.get('category_desc')

            # Validation
            if not category_name or category_desc is None:
                return Response({"message": "All fields are required"}, status=400)

            # Check if user_code is provided for update
            if category_code:
                try:
                    category = Categories.objects.get(category_code=category_code)
                    
                    # Validation
                    # Check for duplicates excluding current category using primary key (id)
                    if Categories.objects.filter(name=category_name).exclude(id=category.id).exists():
                        return Response({"message": "Category with the same name already exists"}, status=409)


                    # Update category data
                    category.name = category_name
                    category.desc = category_desc

                    category.save()
                    category_data = CategorySerializer(category).data
                    refresh = RefreshToken.for_user(request.user) if request.user.is_authenticated else RefreshToken()
                    
                    return Response({
                        "message": "Category updated successfully!",
                        "token": str(refresh.access_token),
                        "refresh": str(refresh), 
                        "category": category_data}, status=status.HTTP_200_OK)
                
                except User.DoesNotExist:
                    return Response({"message": "Category not found"}, status=status.HTTP_404_NOT_FOUND)

            # Check for duplicate name during new category creation
            # Validation
            if not category_name or category_desc is None:
                return Response({"message": "All fields are required"}, status=400)

            if Categories.objects.filter(name=category_name).exists():
                return Response({"message": "Category with the same name already exists"}, status=409)

            # Create new category

            category = Categories.objects.create(
                name = category_name,
                desc = category_desc
            )
            
            refresh = RefreshToken.for_user(request.user) if request.user.is_authenticated else RefreshToken()
            category_data = CategorySerializer(category).data

            return Response({
                "message": "Category Created Successfully!",
                "token": str(refresh.access_token),
                "refresh": str(refresh),
                "category": category_data
            }, status=status.HTTP_201_CREATED)

        except Exception as e:
            return Response({"message": "Category creations failed", "error": str(e)}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)

    elif request.method == 'GET':
        # If user_code is provided, fetch particular user
        category_code = request.GET.get('category_code')

        if category_code:
            try:
                category = Categories.objects.get(category_code=category_code)
                category_data = CategorySerializer(category).data
                return Response({
                    "id": category_data["id"],
                    "name": category_data["name"],
                    "desc": category_data["desc"],
                    "created_at": category_data["created_at"],
                    "custom_code": category_data["custom_code"],
                    "category_code": category_data["category_code"]
                })
            except CustomUser.DoesNotExist:
                return Response({"message": "Category not found"}, status=404)
        
        # Fetch all users and serialize
        category = Categories.objects.all()
        serializer = CategorySerializer(category, many=True)

        # Loop through serialized data (if you want to format it manually)
        data = []
        for category in serializer.data:
            data.append({
                "id": category["id"],
                "name": category["name"],
                "desc": category["desc"],
                "created_at": category["created_at"],
                "custom_code": category["custom_code"],
                "category_code": category["category_code"]
            })

        return Response({"Categories": data})  # Return formatted data




@api_view(['GET', 'POST'])
def SubCategory(request):
    if request.method == 'POST':
        try:
            category_id = request.data.get('category')  # Foreign key ID
            # subcategory_code = request.data.get('subcategory_code')
            description = request.data.get('description')

            if not category_id or not subcategory_code or not description:
                return Response({"message": "All fields are required"}, status=400)

            try:
                category = Categories.objects.get(id=category_id)
            except Categories.DoesNotExist:
                return Response({"message": "Category not found"}, status=404)

            if SubCategory.objects.filter(subcategory_code=subcategory_code).exists():
                return Response({"message": "SubCategory with this code already exists"}, status=409)

            subcategory = SubCategory.objects.create(
                category=category,
                category_name=category.name,  # Store category name
                category_code=category.category_code,  # Store category code
                # subcategory_code=subcategory_code,
                description=description
            )

            subcategory_data = SubCategorySerializer(subcategory).data
            return Response({
                "message": "SubCategory Created Successfully!",
                "subcategory": subcategory_data
            }, status=status.HTTP_201_CREATED)

        except Exception as e:
            return Response({"message": "SubCategory creation failed", "error": str(e)}, status=500)

    elif request.method == 'GET':
        # subcategory_code = request.GET.get('subcategory_code')

        if subcategory_code:
            try:
                subcategory = SubCategory.objects.get(subcategory_code=subcategory_code)
                subcategory_data = SubCategorySerializer(subcategory).data
                return Response(subcategory_data)
            except SubCategory.DoesNotExist:
                return Response({"message": "SubCategory not found"}, status=404)

        subcategories = SubCategory.objects.all()
        serializer = SubCategorySerializer(subcategories, many=True)

        return Response({"SubCategories": serializer.data})









@api_view(['GET', 'POST'])
@parser_classes([MultiPartParser, FormParser])
def product_list(request):
    if request.method == 'GET':
        products = Product.objects.all()
        serializer = ProductSerializer(products, many=True)
        return Response(serializer.data)
    
    if request.method == 'POST':
        serializer = ProductSerializer(data=request.data)
        if serializer.is_valid():
            product = serializer.save()
            return Response(serializer.data, status=201)
        return Response(serializer.errors, status=400)


#  for supplier
@api_view(['GET', 'POST'])
def supplier_list(request):
    if request.method == 'GET':
        suppliers = Supplier.objects.all()
        serializer = SupplierSerializer(suppliers, many=True)
        return Response(serializer.data)
    
    if request.method == 'POST':
        serializer = SupplierSerializer(data=request.data)
        if serializer.is_valid():
            serializer.save()
            return Response(serializer.data, status=201)
        return Response(serializer.errors, status=400)