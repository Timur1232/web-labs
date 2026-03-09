#define FFI_LIB "./probe/raylib_ffi/libraylib.so"

// Color, 4 components, R8G8B8A8 (32bit)
typedef struct Color {
    unsigned char r;        // Color red value
    unsigned char g;        // Color green value
    unsigned char b;        // Color blue value
    unsigned char a;        // Color alpha value
} Color;

void InitWindow(int width, int height, const char *title);
void CloseWindow(void);
bool WindowShouldClose(void);
void ClearBackground(Color color);
void BeginDrawing(void);
void EndDrawing(void);

void SetTargetFPS(int fps);

void PollInputEvents(void);

bool IsKeyPressed(int key);
bool IsKeyPressedRepeat(int key);
bool IsKeyDown(int key);
bool IsKeyReleased(int key);
bool IsKeyUp(int key);
int GetKeyPressed(void);
int GetCharPressed(void);
void SetExitKey(int key);

void DrawText(const char *text, int posX, int posY, int fontSize, Color color);

void DrawRectangle(int posX, int posY, int width, int height, Color color);
