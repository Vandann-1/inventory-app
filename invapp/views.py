from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.contrib.auth import get_user_model, authenticate
from rest_framework_simplejwt.tokens import RefreshToken
from rest_framework import status
from .serializers import UserSerializer, CategorySerializer
from .models import Categories, CustomUser
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


# User registration
@api_view(['POST'])
def register(request):
    try:
        password = request.data.get('password')
        email = request.data.get('email')
        full_name = request.data.get('full_name')
        mobile_no = request.data.get('mobile_no')

        if not password or not email or not full_name:
            return Response({"message": "All fields are required"}, status=status.HTTP_400_BAD_REQUEST)

        if User.objects.filter(email=email).exists():
            return Response({"message": "User with the same email already exists"}, status=status.HTTP_409_CONFLICT)
        
        if User.objects.filter(mobile_no=mobile_no).exists():
            return Response({"message": "User with the same mobile no already exists"}, status=status.HTTP_409_CONFLICT)

        # Assuming full_name contains both first and last name
        name_parts = full_name.strip().split(' ', 1)
        first_name = name_parts[0]
        last_name = name_parts[1] if len(name_parts) > 1 else ''
        full_name = first_name+' '+last_name

        user = User.objects.create_user(
            username=full_name, 
            password=password, 
            email=email,
            first_name=first_name,
            last_name=last_name,
            mobile_no=mobile_no
        )
        
        refresh = RefreshToken.for_user(user)
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
@api_view(['GET', 'POST'])
def categories(request):                        
    if request.method == 'GET':
        # Retrieve all categories
        categories = Categories.objects.all()
        serializer = CategorySerializer(categories, many=True)

        data = []
        for categories in serializer.data:
            data.append({
                "id": categories["id"],
                "name": categories["name"],
                "desc": categories["desc"],
                "created_on": categories["created_at"]
            })
        return Response({"Categories": data})   # Return formatted data

    elif request.method == 'POST':
        category_name = request.data.get('category_name')
        category_desc = request.data.get('category_desc')


        # Validation
        if not category_name or category_desc is None:
            return Response({"message": "All fields are required"}, status=404)

        if Categories.objects.filter(name=category_name).exists():
            return Response({"message": "Category with the same name already exists"}, status=409)
        
        # Creating category
        # Get India Time Zone
        india_tz = pytz.timezone('Asia/Kolkata')
        current_time = timezone.now().astimezone(india_tz)

        # Create Category
        category = Categories.objects.create(
            name=category_name,
            desc=category_desc,
            created_at=current_time
        )
        serializer = CategorySerializer(category)

        # Token for user if authenticated
        refresh = RefreshToken.for_user(request.user) if request.user.is_authenticated else RefreshToken()

        return Response({
            "message": "Category Created Successfully!",
            "token": str(refresh.access_token),
            "refresh": str(refresh),
            "data": serializer.data
            }, status=201)
    
    # return list like users
    # add function for production

# To show the list of all users and perform action on users
@api_view(['GET', 'POST'])
def users(request):
    if request.method == 'GET':
        users = User.objects.all()  # Fetch all users
        serializer = UserSerializer(users, many=True)  #  Use correct serializer

        #  Loop through serialized data (if you want to format it manually)
        data = []
        for user in serializer.data:
            data.append({
                "id": user["id"],
                "username": user["username"],
                "email": user["email"],
                "mobile_no": user["mobile_no"],
                "status": "1" if user["is_active"] else "0"  # if is_active = true then 1 else 0
            })

        return Response({"users": data})   # Return formatted data
    # to update user Active & Inactive Status
    elif request.method == 'POST':
        action = request.data.get('action')
        user_id = request.data.get('user_id')

        if not action or not user_id:
            return Response({"message": "Missing action or user_id"}, status=400)

        if action not in {'active', 'inactive'}:
            return Response({"message": "Invalid action"}, status=400)

        try:
            user = CustomUser.objects.get(id=user_id)

            user.is_active = True if action == 'active' else False
            user.save()

            status_message = "activated" if user.is_active else "deactivated"
            return Response({"message": f"User {status_message} successfully"}, status=200)

        except CustomUser.DoesNotExist:
            return Response({"message": "User not found"}, status=404)

        except Exception as e:
            return Response({"message": f"Error: {str(e)}"}, status=500)

